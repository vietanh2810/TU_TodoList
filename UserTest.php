<?php

require_once './User.php';
require_once './EmailAPI.php';
require_once './Item.php';

class UserTest extends PHPUnit\Framework\TestCase {

    private EmailAPI $emailAPI;

    public function setUp(): void
    {
        $this->emailAPI = $this->getMockBuilder(EmailAPI::class)
            ->onlyMethods(['checkEmail','sendNotif'])
            ->getMock();

        parent::setUp();
    }

    public function testIsValidUser()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009','Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        $this->assertTrue($sut->isValid());
    }

    public function testIsInvalidUser()
    {
        // Invalid user due to the incorrect password format (missing uppercase character)
        $sut = new User('invalid_email@gmail.com', 'Jane', 'Doe', '15-02-2005', 'invalid1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        $this->assertFalse($sut->isValid());
    }

    public function testCannotCreateTodoList()
    {
        // Invalid user from too young age
        $sut = new User('invalid_email@gmail.com', 'Jane', 'Doe', '15-02-2015', 'Abctest123.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        $item = new Item('Task1', 'content task 1','2023-04-25 14:30:00');
        $this->assertFalse($sut->add($item)); //cannot create todolist since invalid user(too young)
    }

    public function TestValidateItem()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);
        // Test valid item
        $item1 = new Item('Task 1', 'Some content', date('Y-m-d H:i:s'));
        $result1 = $sut->add($item1);
        $this->assertTrue($result1);
    }

    public function testItemDoublon()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        // Test valid item
        $item1 = new Item('Task 1', 'Some content', date('Y-m-d H:i:s'));
        $result1 = $sut->add($item1);

        // Test duplicate name 'Task 1' appear twice
        $item2 = new Item('Task 1', 'Some other content', date('Y-m-d H:i:s', strtotime('+1 minute')));
        $result2 = $sut->add($item2);
        $this->assertFalse($result2);
    }

    public function testContentLength()
    {
        $sut = new User('valid_email@gmail.fr', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        // Test content length > 1000 char
        $content = str_repeat('A', 1001);
        $item3 = new Item('Task 2', $content, date('Y-m-d H:i:s', strtotime('+2 minutes')));
        $result3 = $sut->add($item3);
        $this->assertFalse($result3);
    }

    public function testMaximumTimeItem()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        $item1 = new Item('Task 1', 'Some content', date('Y-m-d H:i:s'));
        $sut->add($item1);

        $item4 = new Item('Task 3', 'Some other content', date('Y-m-d H:i:s', strtotime('+33 minutes')));
        $result4 = $sut->add($item4);
        $this->assertFalse($result4);
    }

    public function testEmailItem()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        for ($i = 1; $i <= 7; $i++) {
            $item = new Item("Task $i", "Some content for task $i", date('Y-m-d H:i:s', strtotime("+$i minutes")));
            $sut->add($item);
        }

        // Expect notification after 8th item is added
        $this->emailAPI->expects($this->once())
            ->method('sendNotif')
            ->with('U can add only 2 more items!');

        $item = new Item("Task 8", "Some content for task 8", date('Y-m-d H:i:s', strtotime("+8 minutes")));
        $sut->add($item);

    }

    public function testMaxItem()
    {
        $sut = new User('valid_email@gmail.com', 'John', 'Doe', '20-01-2009', 'Kinkute1.', $this->emailAPI);

        $this->emailAPI->expects($this->any())
            ->method('checkEmail')
            ->willReturn(true);

        // Add 2 more items to reach the limit of 10
        for ($i = 1; $i <= 10; $i++) {
            $item = new Item("Task $i", "Some content for task $i", date('Y-m-d H:i:s', strtotime("+$i minutes")));
            $result = $sut->add($item);
            $this->assertTrue($result);
        }

        // Add the 11th item to exceed the limit of 10
        $item11 = new Item("Task 11", "Some content for task 11", date('Y-m-d H:i:s', strtotime("+11 minutes")));
        $result11 = $sut->add($item11);
        $this->assertFalse($result11);
    }

}
