<?php echo 'test';
//phpinfo();
class Customer {
    private $firstname;
    private $lastname;
    public function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}
$customer = new Customer('John', 'Doe');
$greeting = function ($message) {
    return "$message $this->firstname $this->lastname!";
};
echo $greeting->call($customer, 'Hello');
