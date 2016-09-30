<?php

include("Employee.php");

class EmployeeStorage {

    private $conn;

    public function __construct() {
        $userName = getenv('DBAAS_USER_NAME') ? getenv('DBAAS_USER_NAME') : 'oracle';
        $password = getenv('DBAAS_USER_PASSWORD') ? getenv('DBAAS_USER_PASSWORD') : 'oracle12';
        $default_descriptor = getenv('DBAAS_DEFAULT_CONNECT_DESCRIPTOR') ? getenv('DBAAS_DEFAULT_CONNECT_DESCRIPTOR') : 'localhost/XE';
        $this->conn = oci_connect($userName, $password, $default_descriptor);
        if (!$this->conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            echo "connection error. Failed.....";
        }
    }

    public function getAll() {
        $data = Array();
        $query = 'SELECT * FROM EMPLOYEE';

        $stid = oci_parse($this->conn, $query);
        oci_execute($stid);

        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $employee = new Employee();
            $employee->id = $row['ID'];
            $employee->firstName = $row['FIRSTNAME'];
            $employee->lastName = $row['LASTNAME'];
            $employee->phone = $row['PHONE'];
            $employee->birthDate = $row['BIRTHDATE'];
            $employee->title = $row['TITLE'];
            $employee->dept = $row['DEPARTMENT'];
            $employee->email = $row['EMAIL'];
            array_push($data, $employee);
        }
        return $data;
    }

    public function search($criteria, $value) {
        $data = Array();
		if ($criteria=='department'){
			$filter = 'DEPARTMENT'; 
		}else if ($criteria=='lastname'){
			$filter = 'LASTNAME';
		}else if ($criteria=='title'){
			$filter = 'TITLE';
		}		
		$query = 'SELECT * FROM EMPLOYEE WHERE ' . $filter . '=:value';
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ':value', $value, -1);
        oci_execute($stmt);
        while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $employee = new Employee();
            $employee->id = $row['ID'];
            $employee->firstName = $row['FIRSTNAME'];
            $employee->lastName = $row['LASTNAME'];
            $employee->phone = $row['PHONE'];
            $employee->birthDate = $row['BIRTHDATE'];
            $employee->title = $row['TITLE'];
            $employee->dept = $row['DEPARTMENT'];
            $employee->email = $row['EMAIL'];
            array_push($data, $employee);
        }
        return $data;
    }

    public function delete($id) {
        $query = 'DELETE FROM EMPLOYEE WHERE ID = :id';
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ':id', $id, -1);
        oci_execute($stmt);
    }

    public function update($employee) {
        $query = 'UPDATE EMPLOYEE SET FIRSTNAME=:firstName, LASTNAME=:lastName, PHONE=:phone, BIRTHDATE=:birthdate, TITLE=:title, DEPARTMENT=:department, EMAIL=:email '
                . 'WHERE ID=:id';
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ':firstName', $employee->firstName);
        oci_bind_by_name($stmt, ':lastName', $employee->lastName);
        oci_bind_by_name($stmt, ':phone', $employee->phone);
        oci_bind_by_name($stmt, ':birthdate', $employee->birthDate);
        oci_bind_by_name($stmt, ':title', $employee->title);
        oci_bind_by_name($stmt, ':department', $employee->dept);
        oci_bind_by_name($stmt, ':email', $employee->email);
        oci_bind_by_name($stmt, ':id', $employee->id);
        oci_execute($stmt);
    }

    public function save($employee) {
        $query = 'INSERT INTO EMPLOYEE (ID, FIRSTNAME, LASTNAME, EMAIL, PHONE, BIRTHDATE, TITLE, DEPARTMENT) '
                . 'VALUES(employee_seq.nextval,:firstName,:lastName,:email,:phone,:birthdate,:title,:department)';
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ':firstName', $employee->firstName);
        oci_bind_by_name($stmt, ':lastName', $employee->lastName);
        oci_bind_by_name($stmt, ':phone', $employee->phone);
        oci_bind_by_name($stmt, ':birthdate', $employee->birthDate);
        oci_bind_by_name($stmt, ':title', $employee->title);
        oci_bind_by_name($stmt, ':department', $employee->dept);
        oci_bind_by_name($stmt, ':email', $employee->email);
         oci_execute($stmt);
    }

    public function get($id) {
        $query = 'SELECT * FROM EMPLOYEE WHERE ID=?';
        $statement = $this->dbc->prepare($query);
        $statement->bindParam(1, $id);
        $statement->execute();
        $row = $statement->fetch();
        $employee = new Employee();
        $employee->id = $row['ID'];
        $employee->firstName = $row['FIRSTNAME'];
        $employee->lastName = $row['LASTNAME'];
        $employee->phone = $row['PHONE'];
        $employee->birthDate = $row['BIRTHDATE'];
        $employee->title = $row['TITLE'];
        $employee->dept = $row['DEPARTMENT'];
        $employee->email = $row['EMAIL'];
        return $employee;
    }

}

?>