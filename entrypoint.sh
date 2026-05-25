#!/bin/sh

# Run database setup via our custom Spark command
php spark db:init

# Start Apache in the foreground
exec apache2-foreground
