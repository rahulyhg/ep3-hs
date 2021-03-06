<?php

namespace User\Form;

use User\Entity\User;
use User\Manager\UserManager;
use User\Service\CountryService;
use Zend\Crypt\BlockCipher;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class RegistrationForm extends Form
{

    protected $userManager;
    protected $countryService;
    protected $locale;

    public function __construct(UserManager $userManager, CountryService $countryService, $locale)
    {
        parent::__construct();

        $this->userManager = $userManager;
        $this->countryService = $countryService;
        $this->locale = $locale;
    }

    public function init()
    {
        $this->setName('rf');

        /* Credentials */

        $this->add(array(
            'name' => 'rf-email1',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-email1',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-email',
                ),
                'notes' => 'Please provide your email address',
            ),
        ));

        $this->add(array(
            'name' => 'rf-email2',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-email2',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => ' ',
                'notes' => 'Please type your email address again<br>to prevent typing errors',
            ),
        ));

        $this->add(array(
            'name' => 'rf-pw1',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'rf-pw1',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-pw',
                ),
                'notes' => 'Your password will be safely encrypted',
            ),
        ));

        $this->add(array(
            'name' => 'rf-pw2',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'rf-pw2',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => ' ',
                'notes' => 'Please type your password again<br>to prevent typing errors',
            ),
        ));

        /* Personal data */

        $this->add(array(
            'name' => 'rf-gender',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'rf-gender',
            ),
            'options' => array(
                'label' => 'Salutation',
                'value_options' => User::$genderOptions,
            ),
        ));

        $this->add(array(
            'name' => 'rf-firstname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-firstname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'First & Last name',
            ),
        ));

        $this->add(array(
            'name' => 'rf-lastname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-lastname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Last name',
            ),
        ));

        $this->add(array(
            'name' => 'rf-street',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-street',
                'style' => 'width: 182px;',
            ),
            'options' => array(
                'label' => 'Street & Number',
            ),
        ));

        $this->add(array(
            'name' => 'rf-number',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-number',
                'style' => 'width: 50px;',
            ),
            'options' => array(
                'label' => 'Street number',
            ),
        ));

        $this->add(array(
            'name' => 'rf-zip',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-zip',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Postal code & City',
            ),
        ));

        $this->add(array(
            'name' => 'rf-city',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-city',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));

        $this->add(array(
            'name' => 'rf-country',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'rf-country',
                'value' => strtoupper(substr($this->locale, 3, 2)),
                'style' => 'width: 264px;',
            ),
            'options' => array(
                'label' => 'Country',
                'value_options' => $this->countryService->getNames(),
            ),
        ));

        $this->add(array(
            'name' => 'rf-phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-phone',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Phone number',
            ),
        ));

        /* Add AES encrypted timestamp for security */

        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey('A balrog, a demon of the ancient world. Its foe is beyond any of you, RUN!');

        $this->add(array(
            'name' => 'rf-csrf',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => $blockCipher->encrypt(time()),
            ),
        ));

        $this->add(array(
            'name' => 'rf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Complete registration',
                'class' => 'default-button',
                'style' => 'width: 250px;',
            ),
        ));

        /* Input filters */

        $userManager = $this->userManager;

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'rf-email1' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'useMxCheck' => true,
                            'message' => 'Please type your correct email address here',
                            'messages' => array(
                                'emailAddressInvalidMxRecord' => 'We could not verify your email provider',
                            ),
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                $blacklist = getcwd() . '/data/res/blacklist-emails.txt';

                                if (is_readable($blacklist)) {
                                    $blacklistContent = file_get_contents($blacklist);
                                    $blacklistDomains = explode("\r\n", $blacklistContent);

                                    foreach ($blacklistDomains as $blacklistDomain) {
                                        $blacklistPattern = str_replace('.', '\.', $blacklistDomain);

                                        if (preg_match('/' . $blacklistPattern . '$/', $value)) {
                                            return false;
                                        }
                                    }
                                }

                                return true;
                            },
                            'message' => 'Trash mail addresses are currently blocked - sorry',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) use ($userManager) {
                                if ($userManager->getBy(array('email' => $value))) {
                                    return false;
                                } else {
                                    return true;
                                }
                            },
                            'message' => 'This email address has already been registered',
                        ),
                    ),
                ),
            ),
            'rf-email2' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'rf-email1',
                            'message' => array(
                                'Both email addresses must be identical',
                            ),
                        ),
                    ),
                ),
            ),
            'rf-pw1' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'message' => 'Your password should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'rf-pw2' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'rf-pw1',
                            'message' => array(
                                'Both passwords must be identical',
                            ),
                        ),
                    ),
                ),
            ),
            'rf-firstname' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your firstname here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Your firstname is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \&\'\(\)\+\,\-\.0-9\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'Your firstname contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-lastname' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your lastname here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Your lastname is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \'\+\-\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'Your lastname contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-street' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your street name here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'This street name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \.\'\-\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'This street name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-number' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your street number here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([0-9a-zA-Z\.\-])+$/u',
                            'message' => 'This street number contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-zip' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your postal code here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]{4,6}$/',
                            'message' => 'Please provide a correct postal code',
                        ),
                    ),
                ),
            ),
            'rf-city' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your city here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'This city name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \&\'\(\)\-\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'This city name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-phone' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your phone number here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'This phone number is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \+\/\(\)\-0-9])+$/u',
                            'message' => 'This phone number contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-csrf' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please register over our website only',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) use ($blockCipher) {
                                $time = $blockCipher->decrypt($value);

                                if (! is_numeric($time)) {
                                    return false;
                                }

                                /* Allow form submission after five seconds and until one hour */

                                if (time() - $time < 5 || time() - $time > 60 * 60) {
                                    return false;
                                } else {
                                    return true;
                                }
                            },
                            'message' => 'You were too quick for our system! Please wait some seconds and try again. Thank you!',
                        ),
                    ),
                ),
            ),
        )));
    }

}