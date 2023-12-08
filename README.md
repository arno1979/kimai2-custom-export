# Custom Export

A Kimai 2 bundle providing a custom HTML and PDF export template.

## Installation

First clone it to your Kimai installation `plugins` directory:
```
cd /kimai/var/plugins/
git clone https://github.com/arno1979/kimai2-custom-export.git CustomExportBundle
```

Copy the TTF files from the `Resources/fonts` folder to `var/data/fonts`

And then rebuild the cache:
```
cd /kimai/
bin/console kimai:reload --env=prod
```

## Notes

For this to correctly work you have to add a fee entry containing the hourly rate to the properties of the project you want to create the timesheet for.
Before triggering the custom PDF export in the "Export" section of Kimai2 you have to select a customer AND a project. Otherwise an unspecified error will be triggered and the export will not work!
