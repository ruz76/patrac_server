# get geojson ecr from s3

Lambda microservice thar reads GeoJSON of ECR for specified month and version from S3

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
