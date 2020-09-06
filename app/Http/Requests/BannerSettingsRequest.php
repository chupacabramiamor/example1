<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->isJson()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'theme' => [ 'in:CodGrayWhite,BigStoneTurquoise,SeaweedAtlantis,CharadeJaffa,RhinoShakespeare,CloudBurstGorse,SanJuanGold,BlueChillCanary,AffairBrightSun,PorcelainMalibu,AliceBlueCornflowerBlue,LinkWaterChathamsBlue,SazeracTuscany,CatskillWhiteAquaForest,WhiteMineShaft' ],
            // 'cookieName' => [ 'min:1' ],
            // 'type' => [ 'in:alert,confirm' ],
            // 'blockType' => [ 'in:block,line' ],
            // 'blockPosition' => [ 'in:bottom-left,bottom-right,top-left,top-right,center,top,bottom,top-scroll,bottom-scroll' ],
            // 'buttonDirection' => [ 'in:row,column' ],
            // 'showPoweredBy' => [ 'boolean' ],
            // 'ignoreAllow' => [ 'boolean' ],
            // 'blind.visible' => [ 'boolean' ],
            // 'blind.bgColor' => [ 'regex:/^#[0-9A-f]{6}$/i' ],
            // // 'blind.opacity' => [ '' ],
            // 'animation.type' => [ 'in:yes,no' ],
            // 'animation.delay' => [ 'regex:/^[0-9]+(ms|s)$/i' ],
            // 'animation.duration' => [ 'regex:/^[0-9]+(ms|s)$/i' ],
            // 'link.href' => [ 'regex:/href\s*=\s*(?:[""\'](?<1>[^""\']*)[""\']|(?<1>\S+))/' ],
            // 'accept.byClick' => [ 'boolean' ]
        ];
    }

/**
 * {
  "theme": "CodGrayWhite",
  "cookieName": "vivaprivacy",
  "type": "alert",
  "blockType": "block",
  "blockPosition": "bottom",
  "buttonType": "filled-round",
  "buttonDirection": "row",
  "showPoweredBy": true,
  "ignoreAllow": false,
  "blind": {
    "visible": false,
    "bgColor": "#000000",
    "opacity": ".5"
  },
  "animation": {
    "type": "no",
    "delay": "200ms",
    "duration": "600ms"
  },
  "popup": {
    "styles": {
      "background": "yellow"
    }
  },
  "link": {
    "styles": {
      "color": "red"
    },
    "html": "link text",
    "href": "https://yandex.ru"
  },
  "message": {
    "styles": {
      "color": "red"
    },
    "html": "<p>some text</>"
  },
  "buttonAllow": {
    "html": "Allow",
    "styles": {
      "color": "red"
    }
  },
  "buttonDismiss": {
    "html": "Dismiss",
    "styles": {
      "color": "red"
    }
  },
  "buttonDecline": {
    "html": "Decline",
    "styles": {
      "color": "red"
    }
  },
  "accept": {
    "byScroll": "none",
    "byTime": -1,
    "byClick": false
  }
}
 */
}
