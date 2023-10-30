# Repeat Word Bot v3.0.1

Telegram bot [@RepeatWordBot][0] will help you learn new English words

[![Github actions Build](https://github.com/setnemo/repeat-word-bot/workflows/Actions/badge.svg)](//github.com/setnemo/repeat-word-bot/actions)
[![SonarCloud Coverage](https://sonarcloud.io/api/project_badges/measure?project=setnemo_repeat-word-bot&metric=coverage)](https://sonarcloud.io/component_measures/metric/coverage/list?id=setnemo_repeat-word-bot)
[![SonarCloud Reliability rating](https://sonarcloud.io/api/project_badges/measure?project=setnemo_repeat-word-bot&metric=reliability_rating)](https://sonarcloud.io/component_measures/metric/reliability_rating/list?id=setnemo_repeat-word-bot)
[![SonarCloud Security rating](https://sonarcloud.io/api/project_badges/measure?project=setnemo_repeat-word-bot&metric=security_rating)](https://sonarcloud.io/component_measures/metric/security_rating/list?id=setnemo_repeat-word-bot)
[![SonarCloud Bugs](https://sonarcloud.io/api/project_badges/measure?project=setnemo_repeat-word-bot&metric=bugs)](https://sonarcloud.io/component_measures/metric/bugs/list?id=setnemo_repeat-word-bot)
[![SonarCloud Code Smells](https://sonarcloud.io/api/project_badges/measure?project=setnemo_repeat-word-bot&metric=code_smells)](https://sonarcloud.io/component_measures/metric/code_smells/list?id=setnemo_repeat-word-bot)
[![License](https://poser.pugx.org/pugx/badge-poser/license)](#)

### Environment

This project use [setnemo/bots-environment][1]
```bash
git clone https://github.com/setnemo/bots-environment.git
cd bots-environment
echo "DB_PASSWORD=password\n" > .env
make up
```

### Install

Clone project and build
```bash
git clone https://github.com/setnemo/repeat-word-bot.git
cd repeat-word-bot
make up
```
Create .env
```bash
cat .env.dist > .env
```
Install composer dependencies
```bash
make install
```

### Tests
Build containers for tests
```bash
make test-up
```
Run tests with coverage
```bash
make test-all
```


[0]: https://t.me/RepeatWordBot
[1]: https://github.com/setnemo/bots-environment
