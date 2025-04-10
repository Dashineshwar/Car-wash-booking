<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit632c8d264c5b07bf4ee4eb01f18e711d
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit632c8d264c5b07bf4ee4eb01f18e711d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit632c8d264c5b07bf4ee4eb01f18e711d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit632c8d264c5b07bf4ee4eb01f18e711d::$classMap;

        }, null, ClassLoader::class);
    }
}
