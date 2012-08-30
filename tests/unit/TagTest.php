<?php

class TagTest extends DbTestCase
{
	public $fixtures=array(
		'tags'=>'AppData_Tags_Tag',
	);

    public function testFetch(){
        $name = 'iphone';
        $tag  = AppData_Tags_Tag::fetch($name);
        $this->assertEquals($name, $tag->name);

        $tag2 = AppData_Tags_Tag::fetch( (int)$tag->id );
        
        $this->assertEquals($name, $tag2->name);

        $tag3 = AppData_Tags_Tag::fetch((string)$tag->id);
        $this->assertNull($tag3);
    }

	public function testCreate()
	{
        $tag = new AppData_Tags_Tag();
        $tag->setAttributes( array(
            'name' => 'Apple',
            'category' => 'tech',
        ));
        $this->assertTrue($tag->save());

        $tag2 = AppData_Tags_Tag::model()->findByPk($tag->id);
        $this->assertNotNull($tag2);
        $this->assertEquals($tag->name, $tag2->name);
        $this->assertEquals($tag->category, $tag2->category);
        $this->assertEquals(1, $tag2->frequency);
        $this->assertTrue(time() - $tag2->create_time <=1);
	}

    public function testSuggest(){
        $input = 'i'; 
        //expect iphone(40), ipad(20) 
        $tags  = AppData_Tags_Tag::suggest($input);
        $this->assertEquals(3, count($tags));
        $this->assertEquals('iphone', $tags[0]);
    }

    public function testRename(){
        $from = 'iphone';
        $to  = 'iphone5';
        $tag = AppData_Tags_Tag::fetch($from);
        $this->assertTrue($tag->rename($to));
        $this->assertEquals($to, $tag->name);

        $tag2 = AppData_Tags_Tag::model()->findByPk($tag->id);
        $this->assertEquals($to, $tag2->name);
    }

    public function testIncrement(){
        $name = 'iphone';
        $tag = AppData_Tags_Tag::fetch($name);
        $count = $tag->frequency;
        $tag->increment();
        $tag2 = AppData_Tags_Tag::fetch($name);
        $this->assertEquals( $count+1, $tag2->frequency );
    }
}

