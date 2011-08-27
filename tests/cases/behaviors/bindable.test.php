<?php

App::import('Core', 'Model');
App::import('Model', array('FilebinderPost'));
App::import('Fixture', 'FilebinderPost');

class FilebinderPost extends CakeTestModel{

    public $name = 'FilebinderPost';

    public $actsAs = array('Filebinder.Bindable');
}

class BindableTestCase extends CakeTestCase{

    public $fixtures = array('plugin.filebinder.attachment',
                             'plugin.filebinder.filebinder_post');

    function startTest() {
        $this->FilebinderPost = ClassRegistry::init('FilebinderPost');
        $this->FilebinderPostFixture = ClassRegistry::init('FilebinderPostFixture');
    }

    function endTest() {
        unset($this->FilebinderPost);
        unset($this->FilebinderPostFixture);
    }

    /**
     * testFind
     *
     * @return
     */
    function testFind(){
        $filePath = TMP . 'tests' . DS;
        $this->FilebinderPost->bindFields = array(
                                                  array('field' => 'logo',
                                                        'tmpPath'  => CACHE,
                                                        'filePath' => $filePath,
                                                        ),
                                                  );

        $query = array();
        $query['conditions'] = array('FilebinderPost.id' => 1);
        $result = $this->FilebinderPost->find('first', $query);

        $expected = array(
                          'id' => 1,
                          'title' => 'Title',
                          'body' => 'Filebinder.Bindable Test',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          'logo' => array(
                                          'id' => 100,
                                          'model' => 'FilebinderPost',
                                          'model_id' => 1,
                                          'field_name' => 'logo',
                                          'file_name' => 'logo.png',
                                          'file_content_type' => 'image/png',
                                          'file_size' => '771311',
                                          'created' => '2011-08-22 19:29:32',
                                          'modified' => '2011-08-22 19:29:32',
                                          'file_path' => $filePath . 'FilebinderPost/1/logo/logo.png',
                                          'bindedModel' => 'Attachment'
                                          ),
                          );

        $this->assertEqual($result['FilebinderPost'], $expected);
    }

    /**
     * testFindNoAttachment
     *
     * @return
     */
    function testFindNoAttachment(){
        $filePath = TMP . 'tests' . DS;
        $this->FilebinderPost->bindFields = array(
                                                  array('field' => 'logo',
                                                        'tmpPath'  => CACHE,
                                                        'filePath' => $filePath,
                                                        ),
                                                  );

        $query = array();
        $query['conditions'] = array('FilebinderPost.id' => 401);
        $result = $this->FilebinderPost->find('first', $query);

        $expected = array(
                          'id' => 401,
                          'title' => 'No Attachment',
                          'body' => 'Filebinder.Bindable Test',
                          'created' => '2011-08-23 17:44:58',
                          'modified' => '2011-08-23 12:05:02',
                          'logo' => null
                          );

        $this->assertEqual($result['FilebinderPost'], $expected);
    }

    /**
     * testSave
     *
     * @return
     */
    function testSave(){
        $tmpPath = TMP . 'tests' . DS . 'bindup.png';
        $filePath = TMP . 'tests' . DS;

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->FilebinderPost->bindFields = array(
                                                  array('field' => 'logo',
                                                        'tmpPath'  => CACHE,
                                                        'filePath' => $filePath,
                                                        ),
                                                  );

        $data = array('FilebinderPost' => array('title' => 'Title',
                                                'logo' => array('model' => 'FilebinderPost',
                                                                'field_name' => 'logo',
                                                                'file_name' => 'logo.png',
                                                                'file_content_type' => 'image/png',
                                                                'file_size' => 1395,
                                                                'tmp_bind_path' => $tmpPath
                                                                )));
        $result = $this->FilebinderPost->save($data);
        $id = $this->FilebinderPost->getLastInsertId();
        $query = array();
        $query['conditions'] = array('FilebinderPost.id' => $id);
        $result = $this->FilebinderPost->find('first', $query);

        $this->assertIdentical(file_exists($result['FilebinderPost']['logo']['file_path']), true);

        // rm file
        if (file_exists($result['FilebinderPost']['logo']['file_path'])) {
            unlink($result['FilebinderPost']['logo']['file_path']);
        }
    }

    /**
     * testDelete
     *
     * @return
     */
    function testDelete(){
        $tmpPath = TMP . 'tests' . DS . 'bindup.png';
        $filePath = TMP . 'tests' . DS;

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->FilebinderPost->bindFields = array(
                                                  array('field' => 'logo',
                                                        'tmpPath'  => CACHE,
                                                        'filePath' => $filePath,
                                                        ),
                                                  );

        $data = array('FilebinderPost' => array('title' => 'Title',
                                                'logo' => array('model' => 'FilebinderPost',
                                                                'field_name' => 'logo',
                                                                'file_name' => 'logo.png',
                                                                'file_content_type' => 'image/png',
                                                                'file_size' => 1395,
                                                                'tmp_bind_path' => $tmpPath
                                                                )));
        $result = $this->FilebinderPost->save($data);
        $id = $this->FilebinderPost->getLastInsertId();
        $query = array();
        $query['conditions'] = array('FilebinderPost.id' => $id);
        $result = $this->FilebinderPost->find('first', $query);

        $this->FilebinderPost->delete($id);

        $this->assertIdentical(file_exists($result['FilebinderPost']['logo']['file_path']), false);
    }

    /**
     * testDelete_bindedFileOnly
     *
     * @return
     */
    function testDelete_bindedFileOnly(){
        $tmpPath = TMP . 'tests' . DS . 'bindup.png';
        $filePath = TMP . 'tests' . DS;

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->FilebinderPost->bindFields = array(
                                                  array('field' => 'logo',
                                                        'tmpPath'  => CACHE,
                                                        'filePath' => $filePath,
                                                        ),
                                                  );

        $data = array('FilebinderPost' => array('title' => 'Title',
                                                'logo' => array('model' => 'FilebinderPost',
                                                                'field_name' => 'logo',
                                                                'file_name' => 'logo.png',
                                                                'file_content_type' => 'image/png',
                                                                'file_size' => 1395,
                                                                'tmp_bind_path' => $tmpPath
                                                                )));
        $result = $this->FilebinderPost->save($data);
        $id = $this->FilebinderPost->getLastInsertId();
        $query = array();
        $query['conditions'] = array('FilebinderPost.id' => $id);
        $result = $this->FilebinderPost->find('first', $query);

        $data = array('FilebinderPost' => array('id' => $id,
                                                'title' => 'Title',
                                                'logo' => null,
                                                'delete_logo' => '1',
                                                ));
        $this->FilebinderPost->save($data);

        $this->assertIdentical(file_exists($result['FilebinderPost']['logo']['file_path']), false);
    }

    /**
     * _setTestFile
     *
     * @return
     */
    function _setTestFile($to = null){
        if (!$to) {
            return false;
        }
        $from = APP . 'plugins/filebinder/tests/files/test.png';
        return copy($from, $to);
    }
}