<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit02cb37c3ccc9ece05fa3d24a4bbe251e
{
    public static $files = array (
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
    );

    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'Oktaee\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Oktaee\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit02cb37c3ccc9ece05fa3d24a4bbe251e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit02cb37c3ccc9ece05fa3d24a4bbe251e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
