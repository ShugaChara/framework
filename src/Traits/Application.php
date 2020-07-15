<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace ShugaChara\Framework\Traits;

/**
 * Trait Application
 * @package ShugaChara\Framework\Traits
 */
trait Application
{
    /**
     * Application 名称
     * @return mixed|null
     */
    public function getName()
    {
        return config()->get('app_name', '');
    }

    /**
     * Application 版本
     * @return mixed|null
     */
    public function getVersion()
    {
        return config()->get('app_version', 'v1.0.0');
    }

    /**
    * Application logo
    * @return string
    */
    public function getLogo()
    {
        return <<<LOGO
               `;%$|'                                                             .'!$%!.                                                 
               :%$$$$:                                                            .!$$$$|`                                                
               '|$$$$;                        .!$$%:                               !$$$$|`                                                
     '!%$|'    '|$$$$||$$!   `;|'   `'    .:||;!$$$$:    .:%$$|:       '|$$$|'     !$$$$%|%$%'    .:%$$|:      ;$$|`        `;%$%|'       
    :%$!'`:!`  '%$$$$:  '!`  ;$$;   :;   ;%$$$$$$;      ;$$$$$$$%'    :%$$$$$$%`  .!$$$$|.  ;:   ;$$$$$$$%'   .!$$$!;|'   .!$$$$$$$|.     
    :%%'       :$$$$$:  :!. .!$$;   ;;   !$$!.`%$%'       .:!::|$;   :$$%'        .|$$$$!. .!:     .:!::|$;   .|$$$$!.       `;!:;%$'     
     !$$$$|'   '%$$$!.  :!.  ;$$;  .!:   `|$$$$%:       :$$|' `|$;   :$$;          !$$$%'  .!:   :$$|' `|$;   `|$$$!      .!$$!` '%$:     
         `||`  '%$$!.   ';.  :$$;  `|:     ;$!``''     `||`   .!$;   '%$|.         ;$$$:    ;:  `||`   .!$;    ;$$$;      '$!.   `%$:     
    `|;  :%'   `|$;     .'.   '%$%%;':.   .!$!. '%%'    :%!`:||';!    .!$%;`.'!`   :$%'     ''   :%!`:||';!    '$$$:      .!%;`;%!'!:     
       `'.                                       ;$!              `                                        `     .                  .`    
                                          !|`   `|$!.                                                                                     
                                           ;|:'!$%:                                                                                       

LOGO;
    }

    /**
     * 获取应用程序根目录
     * @return mixed
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * 服务容器注册
     * @param array $services
     */
    public function registerServiceProviders(array $services)
    {
        foreach ($services as $service) {
            (new $service)->register(container());
        }
    }

    /**
     * 应用程序是否已在运行
     * @return bool
     */
    protected function isExecute(): bool
    {
        return (bool) $this->isExecute;
    }
}