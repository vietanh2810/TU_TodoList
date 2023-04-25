<?php

require_once './EmailAPI.php';
require_once './Item.php';

class User {
    private string $email;
    private string $name;
    private string $firstname;
    private string $dob;
    private string $password;
    private array $toDoList;
    private EmailAPI $emailAPI;

    public function __construct($email, $name, $firstname, $dob, $password, $emailAPI) {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->dob = $dob;
        $this->emailAPI = $emailAPI;
        $this->password = $password;
        $this->toDoList = [];
    }

    public function isValid() {
        $dobDateTime = DateTime::createFromFormat('d-m-Y', $this->dob);
        $nowDateTime = new DateTime();
        $diff = $dobDateTime->diff($nowDateTime);

        if (
            isset($this->email)
            && isset($this->name)
            && isset($this->firstname)
            && isset($this->dob)
            && isset($this->password)
            && isset($this->emailAPI)
            && $this->emailAPI->checkEmail($this->email)
            && strlen($this->password) >= 8
            && strlen($this->password) <= 40
            && preg_match('/[a-z]/', $this->password)
            && preg_match('/[A-Z]/', $this->password)
            && preg_match('/[0-9]/', $this->password)
            && $diff->y >= 13
        ) {
            return true;
        } else {
            return false;
        }
    }


    public function add(Item $item): bool {
        // Check if user is valid to create a todolist
        if (!$this->isValid()) return false;

        if (count($this->toDoList) == 10) {
            return false;
        }

        // Check if the item name is already in the to-do list
        foreach ($this->toDoList as $existingItem) {
            if ($existingItem->name === $item->name) {
                return false;
            }
        }

        // Check if the content length is within the limit
        if (strlen($item->content) > 1000) {
            return false;
        }

        // Check if the createdOn time is within 30 minutes of the last item
        if (!empty($this->toDoList)) {
            $lastItem = end($this->toDoList);
            $lastItemCreatedOn = strtotime($lastItem->createdOn);
            $itemCreatedOn = strtotime($item->createdOn);
            $timeDifference = $itemCreatedOn - $lastItemCreatedOn;
            if ($timeDifference > 1800) {
                return false;
            }
        }

        if (count($this->toDoList) == 7) {
            $this->emailAPI->sendNotif('U can add only 2 more items!');
        }

        // Add the item to the to-do list
        $this->toDoList[] = $item;
        return true;
    }

    /**
     * @return array
     */
    public function getToDoList(): array
    {
        return $this->toDoList;
    }

    /**
     * @param array $toDoList
     */
    public function setToDoList(array $toDoList): void
    {
        $this->toDoList = $toDoList;
    }

    /**
     * @return mixed
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param mixed $dob
     */
    public function setDob($dob): void
    {
        $this->dob = $dob;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return EmailAPI
     */
    public function getEmailAPI(): EmailAPI
    {
        return $this->emailAPI;
    }

    /**
     * @param EmailAPI $emailAPI
     */
    public function setEmailAPI(EmailAPI $emailAPI): void
    {
        $this->emailAPI = $emailAPI;
    }
}
