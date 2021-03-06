<?php

namespace nineinchnick\usr\tests\unit;

use nineinchnick\usr\tests\DatabaseTestCase as DatabaseTestCase;
use nineinchnick\usr\models;

class LoginFormTest extends DatabaseTestCase
{
    public $fixtures=array(
        'users'=>'User',
    );

    public static function validDataProvider()
    {
        return array(
            array(
                'scenario' => '',
                'attributes' => array(
                    'username'=>'neo',
                    'password'=>'Test1233',
                ),
            ),
        );
    }

    public static function invalidDataProvider()
    {
        return array(
            array(
                'scenario' => '',
                'attributes' => array(
                    'username'=>'',
                    'password'=>'',
                ),
                'errors ' => array(
                    'username'=>array('Username cannot be blank.'),
                    'password'=>array('Password cannot be blank.'),
                ),
            ),
            array(
                'scenario' => '',
                'attributes' => array(
                    'username'=>'neo',
                    'password'=>'xx',
                ),
                'errors' => array(
                    'password'=>array('Invalid username or password.'),
                ),
            ),
        );
    }

    public static function allDataProvider()
    {
        return array_merge(self::validDataProvider(), self::invalidDataProvider());
    }

    public function testWithBehavior()
    {
        $form = new models\LoginForm;
        $formAttributes = $form->attributes();
        $formRules = $form->rules();
        $formLabels = $form->attributeLabels();
        $form->attachBehavior('captcha', array('class' => 'nineinchnick\usr\components\CaptchaFormBehavior'));
        $behaviorAttributes = $form->getBehavior('captcha')->attributes();
        $behaviorRules = $form->getBehavior('captcha')->rules();
        $behaviorLabels = $form->getBehavior('captcha')->attributeLabels();
        $this->assertEquals(array_merge($formAttributes, $behaviorAttributes), $form->attributes());
        $this->assertEquals(array_merge($formRules, $behaviorRules), $form->rules());
        $this->assertEquals(array_merge($formLabels, $behaviorLabels), $form->attributeLabels());
        $form->detachBehavior('captcha');
        $this->assertEquals($formAttributes, $form->attributes());
        $this->assertEquals($formAttributes, $form->attributes());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid($scenario, $attributes)
    {
        $form = new models\LoginForm($scenario);
        $form->setAttributes($attributes);
        $this->assertTrue($form->validate(), 'Failed with following validation errors: '.print_r($form->getErrors(),true));
        $this->assertEmpty($form->getErrors());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalid($scenario, $attributes, $errors)
    {
        $form = new models\LoginForm($scenario);
        $form->setAttributes($attributes);
        $this->assertFalse($form->validate());
        $this->assertEquals($errors, $form->getErrors());
    }
}
