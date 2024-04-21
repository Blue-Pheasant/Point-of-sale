# CI-CD
In software engineering, CI/CD or CICD is the combined practices of continuous integration (CI) and (more often) continuous delivery or (less often) continuous deployment (CD).

In this project, we will use Github action to build a pipe-line of CI-CD:

![Diagram of the CI-CD pipe line](pipe-line.svg)

When a commit pushed into Github server, github action will be triggered to run a script to test, build and deploy it on hosting server. There is yml script which Github action use for running this process:  

```yml
    name: deploy
    on:
    push:
        branches:
        - master
    jobs:
    build-and-deploy:
        runs-on: ubuntu-latest
        steps:
        - name: Restart Apache Server
            uses: appleboy/ssh-action@master
            with:
            host: ${{ secrets.HOST }}
            username: ${{ secrets.USERNAME }}
            key: ${{ secrets.SSH_PRIVATE_KEY }}
            port: ${{ secrets.PORT }}
            script: |
                cd /home/project/pos
                git pull origin master
                git reset --hard FETCH_HEAD
                git clean -d -f --exclude secrets
                bash auto_install.sh
                chown $(whoami) . # PM2 doesn't recognize root user from Github Actions
```

We used our hosting server to build and deploy intead of use a special server which have role to build source code to container because it just a student project, and not have much time, effort and money to try these services.

In hosting server we write a simple bash script to run after hosting server had been triggerd by Github action:

```bash
    docker-compose down
    docker-compose build
    docker-compose up -d
    docker-compose exec app composer install
    docker-compose exec app cp .env.example .env
    docker cp config.cnf mysql:/config.cnf
    docker cp init.sh mysql:/init.sh
    docker exec -t mysql bash /init.sh
    docker-compose exec app php migrations.php
```