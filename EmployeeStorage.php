<?php

include("Employee.php");

class EmployeeStorage {

    private $conn;

    public function __construct() {
        $this->conn = oci_connect("mika", "pass", "140.86.0.141:1521/PDB1.gse00000504.oraclecloud.internal");
        if (!$this->conn) {
            $e = oci_error();
            //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            echo "connection error. Failed.....";
        }
    }

    public function getAll() {
        $data = Array();
        $query = 'SELECT * FROM SYS.EMPLOYEE';

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
		$query = 'SELECT * FROM SYS.EMPLOYEE WHERE ' . $filter . '=:value';
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
        $query = 'DELETE FROM SYS.EMPLOYEE WHERE ID = :id';
        $stmt = oci_parse($this->conn, $query);
        oci_bind_by_name($stmt, ':id', $id, -1);
        oci_execute($stmt);
    }

    public function update($employee) {
        $query = 'UPDATE SYS.EMPLOYEE SET FIRSTNAME=:firstName, LASTNAME=:lastName, PHONE=:phone, BIRTHDATE=:birthdate, TITLE=:title, DEPARTMENT=:department, EMAIL=:email '
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
        $query = 'INSERT INTO SYS.EMPLOYEE (ID, FIRSTNAME, LASTNAME, EMAIL, PHONE, BIRTHDATE, TITLE, DEPARTMENT) '
                . 'VALUES(SYS.employee_seq.nextval,:firstName,:lastName,:email,:phone,:birthdate,:title,:department)';
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
        $query = 'SELECT * FROM SYS.EMPLOYEE WHERE ID=?';
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