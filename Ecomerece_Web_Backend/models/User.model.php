<?php

class User
{
	public $id;
	public $fullName;
	public $email;
	public $phone;
	public $role;
	public $createdAt;

	public function __construct($data = array())
	{
		$this->id = isset($data['id']) ? $data['id'] : null;
		$this->fullName = isset($data['fullName']) ? $data['fullName'] : '';
		$this->email = isset($data['email']) ? $data['email'] : '';
		$this->phone = isset($data['phone']) ? $data['phone'] : null;
		$this->role = isset($data['role']) ? $data['role'] : 'customer';
		$this->createdAt = isset($data['createdAt']) ? $data['createdAt'] : null;
	}

	public function toArray()
	{
		// This shape is what frontend expects.
		return array(
			'id' => $this->id,
			'fullName' => $this->fullName,
			'email' => $this->email,
			'phone' => $this->phone,
			'role' => $this->role,
			'createdAt' => $this->createdAt
		);
	}
}

