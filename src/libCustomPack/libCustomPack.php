<?php
declare(strict_types=1);
namespace libCustomPack;

use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use Webmozart\PathUtil\Path;

final class libCustomPack{

	final public static function generatePackFromResources(PluginBase $plugin, ?string $packFolderName = null) : ZippedResourcePack {
		$packFolderName ??= $plugin->getName().' Pack';
		$packFolderRegex = str_replace(' ', '\h', $packFolderName);

		$zip = new \ZipArchive();
		$outputFilePath = Path::join($plugin->getDataFolder(), $plugin->getName().'.mcpack');
		$zip->open($outputFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach($plugin->getResources() as $resource){
			if($resource->isFile() and str_contains($resource->getPathname(), $packFolderName)){
				$relativePath = Path::normalize(preg_replace("/.*[\/\\\\]{$packFolderRegex}[\/\\\\].*/U", '', $resource->getPathname()));
				$plugin->saveResource(Path::join($packFolderName, $relativePath), false);
				$zip->addFile(Path::join($plugin->getDataFolder(), $packFolderName, $relativePath), $relativePath);
			}
		}
		$zip->close();
		Filesystem::recursiveUnlink(Path::join($plugin->getDataFolder(), $packFolderName)); // clean up

		// $newFileName = (new ZippedResourcePack($outputFilePath))->getPackName().'.mcpack';
		// assert(!rename($outputFilePath, $newFileName)); // TODO: does the ZippedResourcePack get freed before this happens?

		return new ZippedResourcePack($$outputFilePath);
	}

	final public static function generatePackFromPath(PluginBase $plugin, string $inputFilePath, ?string $packFolderName = null) : ZippedResourcePack {
		$packFolderName ??= $plugin->getName().' Pack';
		$packFolderRegex = str_replace(' ', '\h', $packFolderName);

		$zip = new \ZipArchive();
		$outputFilePath = Path::join($plugin->getDataFolder(), $plugin->getName().'.mcpack');
		$zip->open($outputFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		foreach(
			new \RecursiveDirectoryIterator(
				$inputFilePath,
				\FilesystemIterator::KEY_AS_PATHNAME|\FilesystemIterator::CURRENT_AS_FILEINFO|\FilesystemIterator::SKIP_DOTS
			) as
			$resource
		){
			if($resource->isFile() and str_contains($resource->getPathname(), $packFolderName)){
				$relativePath = Path::normalize(preg_replace("/.*[\/\\\\]{$packFolderRegex}[\/\\\\].*/U", '', $resource->getPathname()));
				copy(Path::join($packFolderName, $relativePath), Path::join($plugin->getDataFolder(), $packFolderName, $relativePath));
				$zip->addFile(Path::join($plugin->getDataFolder(), $packFolderName, $relativePath), $relativePath);
			}
		}
		$zip->close();
		Filesystem::recursiveUnlink(Path::join($plugin->getDataFolder(), $packFolderName)); // clean up

		$newFileName = (new ZippedResourcePack($outputFilePath))->getPackName().'.mcpack';
		assert(!rename($outputFilePath, $newFileName)); // TODO: does the ZippedResourcePack get freed before this happens?

		return new ZippedResourcePack($newFileName);
	}

	final public static function registerResourcePack(ResourcePack $resourcePack) : void {
		$manager = Server::getInstance()->getResourcePackManager();

		$reflection = new \ReflectionClass($manager);

		$property = $reflection->getProperty("resourcePacks");
		$property->setAccessible(true);
		$currentResourcePacks = $property->getValue($manager);
		$currentResourcePacks[] = $resourcePack;
		$property->setValue($manager, $currentResourcePacks);

		$property = $reflection->getProperty("uuidList");
		$property->setAccessible(true);
		$currentUUIDPacks = $property->getValue($manager);
		$currentUUIDPacks[mb_strtolower($resourcePack->getPackId())] = $resourcePack;
		$property->setValue($manager, $currentUUIDPacks);

		$property = $reflection->getProperty("serverForceResources");
		$property->setAccessible(true);
		$property->setValue($manager, true);
	}

	final public static function unregisterResourcePack(ResourcePack $resourcePack) : void {
		$manager = Server::getInstance()->getResourcePackManager();

		$reflection = new \ReflectionClass($manager);

		$property = $reflection->getProperty("resourcePacks");
		$property->setAccessible(true);
		$currentResourcePacks = $property->getValue($manager);
		$key = array_search($resourcePack, $currentResourcePacks);
		if($key !== false){
			unset($currentResourcePacks[$key]);
			$property->setValue($manager, $currentResourcePacks);
		}

		$property = $reflection->getProperty("uuidList");
		$property->setAccessible(true);
		$currentUUIDPacks = $property->getValue($manager);
		if(isset($currentResourcePacks[mb_strtolower($resourcePack->getPackId())])) {
			unset($currentUUIDPacks[mb_strtolower($resourcePack->getPackId())]);
			$property->setValue($manager, $currentUUIDPacks);
		}
	}

}
