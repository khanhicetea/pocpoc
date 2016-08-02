## Demo

![image](https://cloud.githubusercontent.com/assets/4528223/17339044/4774db04-5914-11e6-9c14-513411a828af.png)

## Install

- Webroot is `web/`
- Install dependencies via Composer

```
$ composer install --prefer-dist
```

- Copy `.env.example` to `.env` and updating configs

```
# This is secret key to access the home page with route /_{secret}
SECRET=verysecret

# CouchDB config
DB_URL=https://someone.cloudant.com
DB_NAME=notifications
DB_USER=user
DB_PASS=password
```

## Usage

### View and tracking notifications

Open the url in browser `http://[your-web-url]/_[secret_key_in_env_file]`

### Pushing a notification

Via a GET request
```
curl http://[your-web-url]/[type]/[message-replaced-space-with-dash]
```

Via a POST request
```
curl -XPOST 'message=hello' http://[your-web-url]/[type]
```

With type is 1 of 4 types :
- `s` : Success
- `i` : Info
- `w' : Warning
- `e` : Error
