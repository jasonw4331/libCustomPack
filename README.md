# libCustomPack

[![Poggit-Ci](https://poggit.pmmp.io/ci.shield/jasonw4331/libCustomPack/libCustomPack)](https://poggit.pmmp.io/ci/jasonw4331/libCustomPack/libCustomPack)

A small library for building and registering resource packs with PocketMine-MP
## Usage
This viron was made for developers to build resource packs from plugin resources or from files on the disk, and allows adding new resource packs to the stack without requiring a server restart.
*NOTE*: New resource packs are not automatically updated for previously loaded players. Only new players will see new resource packs.

#### Required imports
The following imports are necessary to use the virion library:
```php
use libCustomPack\libCustomPack;
```

### API
#### Building a resource pack
2 methods are added which allow build a resource pack using the plugin's resource directory or any other given path.
```php
libCustomPack::generatePackFromResources($plugin);
//OR
libCustomPack::generatePackFromPath($plugin, $MyFullFolderPath);
```
#### Registering a resource pack
A resource pack can be added to the resource stack using the `registerResourcePack()` method.
```php
libCustomPack::registerResourcePack($resoucePackInstance);
```
#### Unregistering a resource pack
During a plugin's onDisable() method, it is recommended to call the `unregisterResourcePack()` method to be removed from the resource stack.
```php
libCustomPack::unregisterResourcePack($resoucePackInstance);
```

### SubFolders
If the resource pack is a folder we don't know the exact location of within the given folder, we can use the `$packFolderName` parameter to specify the name of the folder to be found.
The default subfolder name is the plugin's name followed by " Pack".
```php
libCustomPack::generatePackFromResources($plugin, $packFolderName);
//OR
libCustomPack::generatePackFromPath($plugin, $MyFullFolderPath, $packFolderName);
```