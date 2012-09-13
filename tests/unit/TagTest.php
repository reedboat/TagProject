<?php

class TagTest extends WF_DbTestCase
{
    public $fixtures=array(
        'tag'=>'Tag',
    );

    public function testFetch(){
        $name = 'iphone';
        $tag  = Tag::fetch($name);
        $this->assertEquals($name, $tag->name);

        $tag2 = Tag::fetch( (int)$tag->id );

        $this->assertEquals($name, $tag2->name);

        $tag3 = Tag::fetch((string)$tag->id);
        //$this->assertNull($tag3);
    }

    public function testCreate()
    {
        $tag = new Tag();
        $tag->setAttributes( array(
            'name' => 'Apple',
            'category' => 1,
        ));
        $this->assertTrue($tag->save());

        $tag2 = Tag::model()->findByPk($tag->id);
        $this->assertNotNull($tag2);
        $this->assertEquals($tag->name, $tag2->name);
        $this->assertEquals($tag->category, $tag2->category);
        $this->assertEquals(1, $tag2->frequency);
        $this->assertTrue(time() - $tag2->create_time <=1);
    }

    public function testSuggestByPrefix(){
        $prefix = 'i'; 
        //expect iphone(40), ipad(20) 
        $tags  = Tag::model()->suggestByPrefix($prefix);
        $this->assertEquals(2, count($tags));
        $tags  = array_keys($tags);
        $this->assertEquals('iphone', $tags[0]);
        $this->assertEquals('ipad', $tags[1]);
    }


    public function testSuggestByKeyword(){
        $keyword = 'i'; 
        //expect yii(1), iphone(40), ipad(20) 
        $tags  = Tag::model()->suggestByKeyword($keyword);
        $this->assertEquals(3, count($tags));
        $tags  = array_keys($tags);
        $this->assertEquals('iphone', $tags[0]);
        $this->assertEquals('ipad', $tags[1]);
        $this->assertEquals('yii', $tags[2]);

        $keyword = '腾讯';
        $tags  = Tag::model()->suggestByKeyword($keyword);
        $this->assertEquals(3, count($tags));
        $tags  = array_keys($tags);
        $this->assertEquals('腾讯QQ', $tags[0]);
    }

    public function testRename(){
        $from = 'iphone';
        $to  = 'iphone5';
        $tag = Tag::fetch($from);
        $this->assertTrue($tag->rename($to));
        $this->assertEquals($to, $tag->name);

        $tag2 = Tag::model()->findByPk($tag->id);
        $this->assertEquals($to, $tag2->name);
    }

    public function testIncrement(){
        $name = 'iphone';
        $tag = Tag::fetch($name);
        $count = $tag->frequency;
        $tag->increment();
        $tag2 = Tag::fetch($name);
        $this->assertEquals( $count+1, $tag2->frequency );
    }
}

