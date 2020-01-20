# Custom Export

A Kimai 2 bundle providing a custom HTML export template.

## Installation

First clone it to your Kimai installation `plugins` directory:
```
cd /kimai/var/plugins/
git clone https://github.com/arno1979/CustomExportBundle.git
```

And then rebuild the cache: 
```
cd /kimai/
bin/console cache:clear
bin/console cache:warmup
```
