{{/*
Expand the name of the chart.
*/}}
{{- define "larakube.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Create a default fully qualified app name.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
If release name contains chart name it will be used as a full name.
*/}}
{{- define "larakube.fullname" -}}
{{- if .Values.fullnameOverride }}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- $name := default .Chart.Name .Values.nameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}
{{- end }}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "larakube.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "larakube.labels" -}}
helm.sh/chart: {{ include "larakube.chart" . }}
{{ include "larakube.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "larakube.selectorLabels" -}}
app.kubernetes.io/name: {{ include "larakube.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{/*
Create the name of the service account to use
*/}}
{{- define "larakube.serviceAccountName" -}}
{{- if .Values.serviceAccount.create }}
{{- default (include "larakube.fullname" .) .Values.serviceAccount.name }}
{{- else }}
{{- default "default" .Values.serviceAccount.name }}
{{- end }}
{{- end }}

{{/*
Container env: component-specific plain values plus secretEnvs references.
Usage: include "larakube.containerEnv" (dict "root" $ "env" .Values.web.env)
*/}}
{{- define "larakube.containerEnv" -}}
{{- range $key, $value := .env }}
- name: {{ $key }}
  value: {{ $value | toString | quote }}
{{- end }}
{{- range $i, $secretEnv := .root.Values.secretEnvs }}
{{- if not (and $secretEnv.name $secretEnv.secretName $secretEnv.secretKey) }}
{{- fail (printf "secretEnvs[%d]: name, secretName and secretKey are required" $i) }}
{{- end }}
- name: {{ $secretEnv.name }}
  valueFrom:
    secretKeyRef:
      name: {{ $secretEnv.secretName | quote }}
      key: {{ $secretEnv.secretKey | quote }}
{{- end }}
{{- end }}

{{/*
PodDisruptionBudget minAvailable / maxUnavailable. Exactly one must be set; 0 is a valid value.
Usage: include "larakube.pdbSpec" (dict "pdb" .Values.web.pdb "name" "web")
*/}}
{{- define "larakube.pdbSpec" -}}
{{- $hasMin := not (kindIs "invalid" .pdb.minAvailable) }}
{{- $hasMax := not (kindIs "invalid" .pdb.maxUnavailable) }}
{{- if and $hasMin $hasMax }}
{{- fail (printf "%s.pdb: set either minAvailable or maxUnavailable, not both" .name) }}
{{- else if not (or $hasMin $hasMax) }}
{{- fail (printf "%s.pdb.enabled is true: set minAvailable or maxUnavailable" .name) }}
{{- end }}
{{- if $hasMin }}
minAvailable: {{ .pdb.minAvailable }}
{{- else }}
maxUnavailable: {{ .pdb.maxUnavailable }}
{{- end }}
{{- end }}

{{/*
Traefik CRD apiVersion. Traefik v3 only serves traefik.io; traefik.containo.us is the Traefik v2 group.
*/}}
{{- define "larakube.traefikApiVersion" -}}
{{- $version := .Values.web.traefik.version | toString }}
{{- if eq $version "3" }}
{{- "traefik.io/v1alpha1" }}
{{- else if eq $version "2" }}
{{- "traefik.containo.us/v1alpha1" }}
{{- else }}
{{- fail (printf "web.traefik.version must be 3 or 2, got %q" $version) }}
{{- end }}
{{- end }}

{{/*
TLS secret used by the Traefik IngressRoutes.
*/}}
{{- define "larakube.traefikTlsSecretName" -}}
{{- default (printf "%s-cert-secret" (include "larakube.fullname" .)) .Values.web.traefik.tlsSecretName }}
{{- end }}

{{/*
Name suffix for a domain redirect, e.g. www.example.com -> www-example-com
*/}}
{{- define "larakube.domainSlug" -}}
{{- if not . }}
{{- fail "web.traefik.domainRedirects: every entry needs a domain" }}
{{- else if contains "*" . }}
{{- fail (printf "web.traefik.domainRedirects: wildcard domain %q is not supported" .) }}
{{- end }}
{{- . | replace "." "-" | kebabcase }}
{{- end }}
