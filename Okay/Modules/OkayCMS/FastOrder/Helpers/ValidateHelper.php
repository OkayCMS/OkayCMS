<?php

namespace Okay\Modules\OkayCMS\FastOrder\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Validator;
use Okay\Entities\VariantsEntity;

class ValidateHelper implements ExtensionInterface
{
    /** @var Request $request */
    private $request;

    /** @var Validator $validator */
    private $validator;

    /** @var EntityFactory $entityFactory */
    private $entityFactory;

    /** @var FrontTranslations $frontTranslations */
    private $frontTranslations;

    /** @var Settings $settings */
    private $settings;

    public function __construct(
        Request                 $request,
        Validator               $validator,
        EntityFactory           $entityFactory,
        FrontTranslations       $frontTranslations,
        Settings                $settings
    )
    {
        $this->request              = $request;
        $this->validator            = $validator;
        $this->entityFactory        = $entityFactory;
        $this->frontTranslations    = $frontTranslations;
        $this->settings             = $settings;
    }

    public function validateFastOrderHeler($order,$variantId)
    {
        $errors = [];
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entityFactory->get(VariantsEntity::class);

        if (!$this->validator->isName($order->name, true)) {
            $errors[] = $this->frontTranslations->getTranslation('okay_cms__fast_order__form_name_error');
        }

        if (!$this->validator->isPhone($order->phone, true)) {
            $errors[] = $this->frontTranslations->getTranslation('okay_cms__fast_order__form_phone_error');
        }

        if (empty($variantId) || !$variantsEntity->findOne(['id' => $variantId])) {
            $errors[] = $this->frontTranslations->getTranslation('okay_cms__fast_order__wrong_variant');
        }

        $captchaCode =  $this->request->post('captcha_code', 'string');
        if ($this->settings->get('captcha_fast_order') && !$this->validator->verifyCaptcha('captcha_fast_order', $captchaCode)) {
            $errors[] = $this->frontTranslations->getTranslation('okay_cms__fast_order__form_captcha_error');
        }

        return $errors;
    }
}