---
title: Scheduler
description: Configuring a scheduler for the Laravel Task Scheduling feature
---


```yaml
scheduler:
  enabled: true
  schedule: "* * * * *" # every minute
  resources:
    requests:
      cpu: 200m
      memory: 250Mi
    limits:
      cpu: 1120m
      memory: 1Gi
  command:
    - /bin/sh
    - -c
    - php artisan schedule:run
```
