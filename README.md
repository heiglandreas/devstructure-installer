devstructure-installer
======================

Install a Development-structure from a template

This composer-package installs a development-structure from a template into the root-folder of your project.

So you can create a composer-package that contains a folder named ```template``` containing the
folder-layout you want to be created when using the package.

Then you have to include the following in your templates ```composer.json````

```json
{
    "type" : "org-heigl-devstructure",
    "require" : {
        "org_heigl/devstructure-installer": "*"
    }
}
```

For an example have a look at https://github.com/heiglandreas/devstructure
