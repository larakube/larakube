---
title: Configure the Laravel application
description: Configure the Laravel application with environment variables and secrets
---

Every Laravel application needs to be configured with environment variables.
LaraKube provides a way to set these variables in the `values.yml` file and seperates between normal environment variables and secrets.

**values.yml**:

```yaml
env:
  # App
  APP_NAME: "My application"
  APP_ENV: "staging"
  APP_DEBUG: "false"
```

More sensitive information, like database credentials or API keys, should be stored in Kubernetes secrets.

```bash
kubectl create secret generic my-app-env --namespace=my-namespace
```

```bash
kubectl modify-secret my-app-env -n my-namespace
```

```yaml
APP_KEY: ""
DB_PASSWORD: ""
MAIL_USERNAME: ""
MAIL_PASSWORD: ""
```

**Generate the application key**:

You can generate the application key locally with the following command:

```bash
php artisan key:generate --show
```

This command will output the key, which you can then copy into the `APP_KEY` variable in the secret.
The key will not be in the terminal history and is not stored in any file.
