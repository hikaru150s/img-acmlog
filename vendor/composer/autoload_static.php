<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1860181ed02af9ddc77633fedd9574cb
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phpml\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phpml\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ai/php-ml/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1860181ed02af9ddc77633fedd9574cb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1860181ed02af9ddc77633fedd9574cb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
