# PHP-Boxplot

PHP-Boxplot is easy to use for creating boxplots.

<img src="img/BoxplotDetmersReid2023_01.png" width ="300" />　
<img src="img/BoxplotDetmersReid2023_02.png" width ="300" />

## Install

```bash
composer require macocci7/php-boxplot
```

## Usage

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpBoxplot\Boxplot;

    $data = [
        '1st' => [75, 82, 96, 43, 78, 91, 84, 87, 93],
        '2nd' => [66, 74, 62, 100, 72, 68, 59, 76, 65],
        '3rd' => [56, 0, 45, 76, 58, 52, 13, 48, 54, 68],
        '4th' => [68, 32, 56, 92, 67, 72, 45, 76, 48, 73],
        '5th' => [70, 58, 62, 88, 62, 68, 56, 63, 64, 78],
    ];

    $bp = new Boxplot();

    $bp->setData($data)
        ->setLimit(0, 100)
        ->setGridHeightPitch(10)
        ->gridVerticalOn()
        ->outlierOn()
        ->meanOn()
        ->setLabelX('Examination')
        ->setLabelY('Score')
        ->setCaption('Results in 2022')
        ->create()
        ->save('img/BoxplotExample.png');
    ```
- Result

    ![BoxplotExample.png](img/BoxplotExample.png)
    
## Example

preparing.

## License

[MIT](LICENSE)

*Document created: 2023/05/28*
*Document updated: 2023/05/28*

Copyright 2023 macocci7
