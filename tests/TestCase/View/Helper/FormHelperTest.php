<?php

namespace BootstrapUI\Test\TestCase\View\Helper;

use BootstrapUI\View\Helper\FormHelper;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Cake\View\View;

class FormHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Configure::write('Config.language', 'eng');
        Configure::write('App.base', '');
        Configure::write('App.namespace', 'BootstrapUI\Test\TestCase\View\Helper');
        Configure::delete('Asset');
        $this->View = new View();

        $this->Form = new FormHelper($this->View);
        $request = new Request('articles/add');
        $request->here = '/articles/add';
        $request['controller'] = 'articles';
        $request['action'] = 'add';
        $request->webroot = '';
        $request->base = '';
        $this->Form->Url->request = $this->Form->request = $request;

        $this->article = [
            'schema' => [
                'id' => ['type' => 'integer'],
                'author_id' => ['type' => 'integer', 'null' => true],
                'title' => ['type' => 'string', 'null' => true],
                'body' => 'text',
                'published' => ['type' => 'boolean', 'length' => 1, 'default' => 0],
                '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
            ],
            'required' => [
                'author_id' => true,
                'title' => true,
            ]
        ];

        Security::salt('foo!');
        Router::connect('/:controller', ['action' => 'index']);
        Router::connect('/:controller/:action/*');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Form, $this->View);
        TableRegistry::clear();
    }

    public function testBasicTextInput()
    {
        unset($this->article['required']['title']);
        $this->Form->create($this->article);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testStaticControl()
    {
        unset($this->article['required']['title']);
        $this->article['defaults']['title'] = 'foo bar';
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['type' => 'staticControl']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'p' => ['class' => 'form-control-static'],
            'foo bar',
            '/p',
            'input' => [
                'type' => 'hidden',
                'name' => 'title',
                'id' => 'title',
                'value' => 'foo bar',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
        $this->assertSame(['title' => 'foo bar'], $this->Form->fields);

        $this->Form->fields = [];

        $result = $this->Form->input('title', ['type' => 'staticControl', 'hiddenField' => false]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'p' => ['class' => 'form-control-static'],
            'foo bar',
            '/p',
            '/div'
        ];
        $this->assertHtml($expected, $result);
        $this->assertEmpty($this->Form->fields);
    }

    public function testNoLabelTextInput()
    {
        unset($this->article['required']['title']);
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['label' => false]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testLabelledTextInput()
    {
        unset($this->article['required']['title']);
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['label' => 'Custom Title']);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Custom Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testArrayLabelledTextInput()
    {
        unset($this->article['required']['title']);
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['label' => ['foo' => 'bar', 'text' => 'Custom Title']]);
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title', 'foo' => 'bar'],
            'Custom Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testBasicPasswordInput()
    {
        $this->article['schema']['password'] = ['type' => 'string'];
        $this->Form->create($this->article);

        $result = $this->Form->input('password');
        $expected = [
            'div' => ['class' => 'form-group'],
            'label' => ['class' => 'control-label', 'for' => 'password'],
            'Password',
            '/label',
            'input' => [
                'type' => 'password',
                'name' => 'password',
                'id' => 'password',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testRequiredTextInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testErroredTextInput()
    {
        $this->article['errors'] = [
            'title' => ['error message'],
            'published' => ['error']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required has-error'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control ',
                'required' => 'required'
            ],
            ['p' => ['class' => 'help-block']],
            'error message',
            '/p',
            '/div'
        ];

        $this->Form->end();

        $this->Form->create($this->article, ['align' => 'horizontal']);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required has-error'],
            'label' => ['class' => 'control-label col-md-2', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-6']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control ',
                'required' => 'required'
            ],
            ['p' => ['class' => 'help-block']],
            'error message',
            '/p',
            '/div',
            '/div'
        ];

        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published');
        $expected = [
            'div' => ['class' => 'form-group has-error'],
            ['div' => ['class' => 'col-md-offset-2 col-md-6']],
            ['div' => ['class' => 'checkbox']],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1,
            ]],
            'Published',
            '/label',
            '/div',
            ['p' => ['class' => 'help-block']],
            'error',
            '/p',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testAddonPrependedInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['prepend' => '@']);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'input-group']],
            'span' => ['class' => 'input-group-addon'],
            '@',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testAddonAppendedInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['append' => '@']);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'span' => ['class' => 'input-group-addon'],
            '@',
            '/span',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testButtonPrependedInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['prepend' => $this->Form->button('GO')]);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'input-group']],
            'span' => ['class' => 'input-group-btn'],
            'button' => ['type' => 'submit', 'class' => 'btn'],
            'GO',
            '/button',
            '/span',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testButtonAppendedInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['append' => $this->Form->button('GO')]);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'input-group']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'span' => ['class' => 'input-group-btn'],
            'button' => ['type' => 'submit', 'class' => 'btn'],
            'GO',
            '/button',
            '/span',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testBasicRadioInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('published', ['type' => 'radio', 'options' => ['Yes', 'No']]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => '',
            ],
            ['div' => ['class' => 'radio']],
            ['label' => ['for' => 'published-0']],
            ['input' => [
                'type' => 'radio',
                'name' => 'published',
                'value' => 0,
                'id' => 'published-0',
            ]],
            'Yes',
            '/label',
            '/div',
            ['div' => ['class' => 'radio']],
            ['label' => ['for' => 'published-1']],
            ['input' => [
                'type' => 'radio',
                'name' => 'published',
                'value' => 1,
                'id' => 'published-1',
            ]],
            'No',
            '/label',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testInlineRadioInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('published', [
            'inline' => true,
            'type' => 'radio',
            'options' => ['Yes', 'No']
        ]);
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => '',
            ],
            ['div' => ['class' => 'radio']],
            ['label' => ['for' => 'published-0']],
            ['input' => [
                'type' => 'radio',
                'name' => 'published',
                'value' => 0,
                'id' => 'published-0',
            ]],
            'Yes',
            '/label',
            '/div',
            ['div' => ['class' => 'radio']],
            ['label' => ['for' => 'published-1']],
            ['input' => [
                'type' => 'radio',
                'name' => 'published',
                'value' => 1,
                'id' => 'published-1',
            ]],
            'No',
            '/label',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published', [
            'label' => ['class' => 'radio-inline'],
            'type' => 'radio',
            'options' => ['Yes', 'No']
        ]);
        $this->assertHtml($expected, $result);
    }

    public function testBasicCheckboxInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('published');
        $expected = [
            'div' => ['class' => 'checkbox'],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1
            ]],
            'Published',
            '/label',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testInlineCheckboxInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('published', ['inline' => true]);
        $expected = [
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['class' => 'checkbox-inline', 'for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1
            ]],
            'Published',
            '/label'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testBasicFormCreate()
    {
        $result = $this->Form->create($this->article);
        $expected = [
            'form' => [
                'method' => 'post',
                'accept-charset' => 'utf-8',
                'role' => 'form',
                'action' => '/articles/add',
            ],
            'div' => ['style' => 'display:none;'],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testFormCreateWithTemplatesFile()
    {
        unset($this->article['required']['title']);
        $this->Form->create($this->article, ['templates' => 'custom_templates']);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'custom-container form-group'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testInlineFormCreate()
    {
        $result = $this->Form->create($this->article, ['class' => 'form-inline']);
        $expected = [
            'form' => [
                'method' => 'post',
                'accept-charset' => 'utf-8',
                'role' => 'form',
                'action' => '/articles/add',
                'class' => 'form-inline',
            ],
            'div' => ['style' => 'display:none;'],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testHorizontalFormCreate()
    {
        $result = $this->Form->create($this->article, ['align' => 'horizontal']);
        $expected = [
            'form' => [
                'method' => 'post',
                'accept-charset' => 'utf-8',
                'role' => 'form',
                'action' => '/articles/add',
                'class' => 'form-horizontal',
            ],
            'div' => ['style' => 'display:none;'],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => [
                'class' => 'control-label col-md-2',
                'for' => 'title'
            ],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-6']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required',
            ],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published');
        $expected = [
            'div' => ['class' => 'form-group'],
            ['div' => ['class' => 'col-md-offset-2 col-md-6']],
            ['div' => ['class' => 'checkbox']],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1,
            ]],
            'Published',
            '/label',
            '/div',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testCustomGrid()
    {
        $this->Form->create($this->article, [
            'align' => [
                'left' => 3,
                'middle' => 5,
                'right' => 4
            ]
        ]);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => [
                'class' => 'control-label col-md-3',
                'for' => 'title'
            ],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-5']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required',
            ],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testHorizontalFormCreateFromConfig()
    {
        $this->Form->config([
            'align' => 'horizontal',
            'templateSet' => [
                'horizontal' => [
                    'checkboxFormGroup' => '<div class="%s"><div class="my-checkbox">{{label}}</div>{{error}}{{help}}</div>'
                ]
            ]
        ]);
        $result = $this->Form->create($this->article);
        $expected = [
            'form' => [
                'method' => 'post',
                'accept-charset' => 'utf-8',
                'role' => 'form',
                'action' => '/articles/add',
                'class' => 'form-horizontal',
            ],
            'div' => ['style' => 'display:none;'],
            'input' => [
                'type' => 'hidden',
                'name' => '_method',
                'value' => 'POST'
            ],
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('title');
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => [
                'class' => 'control-label col-md-2',
                'for' => 'title'
            ],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-6']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required',
            ],
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published');
        $expected = [
            'div' => ['class' => 'form-group'],
            ['div' => ['class' => 'col-md-offset-2 col-md-6']],
            ['div' => ['class' => 'my-checkbox']],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1,
            ]],
            'Published',
            '/label',
            '/div',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testBasicButton()
    {
        $result = $this->Form->button('Submit');
        $expected = [
            'button' => ['class' => 'btn', 'type' => 'submit'],
            'Submit',
            '/button'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testStyledButton()
    {
        $result = $this->Form->button('Submit', ['class' => 'success']);
        $expected = [
            'button' => ['class' => 'btn-success btn', 'type' => 'submit'],
            'Submit',
            '/button'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testMultipleCheckboxInput()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('users', [
            'multiple' => 'checkbox',
            'options' => [
                1 => 'User 1',
                2 => 'User 2'
            ]
        ]);
        $expected = [
            ['div' => ['class' => 'form-group']],
            ['label' => []],
            'Users',
            '/label',
            'input' => [
                'type' => 'hidden',
                'name' => 'users',
                'value' => '',
            ],
            ['div' => ['class' => 'checkbox']],
            ['label' => ['for' => 'users-1']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'users[]',
                    'id' => 'users-1',
                    'value' => 1,
                ]
            ],
            'User 1',
            '/label',
            '/div',
            ['div' => ['class' => 'checkbox']],
            ['label' => ['for' => 'users-2']],
            [
                'input' => [
                    'type' => 'checkbox',
                    'name' => 'users[]',
                    'id' => 'users-2',
                    'value' => 2,
                ]
            ],
            'User 2',
            '/label',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testHelpText()
    {
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'p' => ['class' => 'help-block'],
            'help text',
            '/p',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'checkbox'],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1
            ]],
            'Published',
            '/label',
            'p' => ['class' => 'help-block'],
            'help text',
            '/p',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->article['errors'] = [
            'title' => ['error message']
        ];
        $this->Form->create($this->article);

        $result = $this->Form->input('title', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'form-group required has-error'],
            'label' => ['class' => 'control-label', 'for' => 'title'],
            'Title',
            '/label',
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control ',
                'required' => 'required'
            ],
            ['p' => ['class' => 'help-block']],
            'error message',
            '/p',
            'p' => ['class' => 'help-block'],
            'help text',
            '/p',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testHelpTextHorizontal()
    {
        $this->Form->create($this->article, ['align' => 'horizontal']);

        $result = $this->Form->input('title', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'form-group required'],
            'label' => ['class' => 'control-label col-md-2', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-6']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control',
                'required' => 'required'
            ],
            'p' => ['class' => 'help-block'],
            'help text',
            '/p',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Form->input('published', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'form-group'],
            ['div' => ['class' => 'col-md-offset-2 col-md-6']],
            ['div' => ['class' => 'checkbox']],
            'input' => [
                'type' => 'hidden',
                'name' => 'published',
                'value' => 0,
            ],
            'label' => ['for' => 'published'],
            ['input' => [
                'type' => 'checkbox',
                'name' => 'published',
                'id' => 'published',
                'value' => 1,
            ]],
            'Published',
            '/label',
            '/div',
            ['p' => ['class' => 'help-block']],
            'help text',
            '/p',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);

        $this->article['errors'] = [
            'title' => ['error message']
        ];
        $this->Form->create($this->article, ['align' => 'horizontal']);

        $result = $this->Form->input('title', ['help' => 'help text']);
        $expected = [
            'div' => ['class' => 'form-group required has-error'],
            'label' => ['class' => 'control-label col-md-2', 'for' => 'title'],
            'Title',
            '/label',
            ['div' => ['class' => 'col-md-6']],
            'input' => [
                'type' => 'text',
                'name' => 'title',
                'id' => 'title',
                'class' => 'form-control ',
                'required' => 'required'
            ],
            ['p' => ['class' => 'help-block']],
            'error message',
            '/p',
            'p' => ['class' => 'help-block'],
            'help text',
            '/p',
            '/div',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
}
