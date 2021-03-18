![EmmCall Logo](https://github.com/Payfu/emmcall-fwk/blob/master/App/Templates/Public/favicon/favicon-128.png?raw=true)

# Note

**This my personal MVC project with bundles and routes**

*It's less heavy than Symfony, it's not perfect, but it works well.*

## Composer

```
composer require emmcall/emmcall-fwk
```

## Unix Install
Move directories into the root.
```
mv -u vendor/emmcall/emmcall-fwk/* ./
```

Move .gitignore and .htaccess files.
```
mv -u vendor/emmcall/emmcall-fwk/.* ./
```

Remove emmcall directory.
```
rm -rf vendor/emmcall
```

## Windows Install
Move directories into the root.
```
xcopy /E /Q vendor\emmcall\emmcall-fwk\* .\
```

Remove emmcall directory.
```
rmdir vendor\emmcall
```

## CSS Preprocessors
To use Bootstrap SCSS
```
xcopy /E /Q vendor\twbs\bootstrap\scss\* .\App\Templates\Public\scss
```
Preprocessors config
```
Input Folder
App\Templates\Public\scss

Output Folder
App\Templates\Public\css\bootstrap

Compiler Options
--style compressed --sourcemap=none
```

*Time is lacking to explain how it works, sorry !*

**Emmanuel C.**
