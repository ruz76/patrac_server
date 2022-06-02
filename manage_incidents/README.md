# Manage incidents

Lambda microservice thar allows to store incident in DynamoDB and delete all test incidents older than 5 days.

## Setup

### Install npm packages
```bash
$ npm install
```

### Deploy!
```bash:development
$ serverless deploy -v
```

or production
```bash:production
$ serverless deploy -v --stage live
```
