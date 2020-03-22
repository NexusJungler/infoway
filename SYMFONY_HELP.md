# Working with multiple databases (Symfony v.4)

## create database
> php bin/console doctrine:database:create --connection=[connection_name]

## Create entities
> php bin/console make:entity `[namespace]\[entity_name]`

## Create migrations
> php bin/console doctrine:migrations:diff --em=[connection_name] --configuration=/config/doctrine_migrations_[default or customer].yaml

## Execute migrations
> php bin/console doctrine:migrations:migrate --em=[connection_name] --configuration=/config/doctrine_migrations_[default or customer].yaml

## Execute fixtures
> php bin/console doctrine:fixtures:load --group=[group_name] --em=[connection_name]


