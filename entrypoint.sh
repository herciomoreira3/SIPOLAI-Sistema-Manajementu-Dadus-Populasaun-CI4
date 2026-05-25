#!/bin/sh

# Run database setup
php check_and_seed.php

# Start Apache in the foreground
exec apache2-foreground
