<?php

// require_once ('session.php');

//     if(!isset($_SESSION['sessobj'])) {
//         $_SESSION['sessobj'] = new sessionManager;
//         http_response_code(429);
//         exit();  // exit early if user not allowed
//     }

//     if($_SESSION['sessobj']->is_rate_limited()) {
//         http_response_code(429);
//         exit();
//     }

//     if (!$_SESSION['sessobj']->is_corret_origin () ) {
//         http_response_code(401);
//         exit();
//     }

header('Content-type: application/json');
$dbURI = 'mysql:host=localhost;port=8889;dbname=wildlife-watcher';
$dbconn = new PDO($dbURI, 'user1', 'user1');
// $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$resp_code = http_response_code(400);
$resp_body = ['error' => 'true'];

session_start();

if (!isset($_SESSION['is-logged-in'])) {
    $_SESSION['is-logged-in'] = false;
}

if (isset($_GET['page'])) {
    switch($_GET['page']) {

        // Register //
        case 'register':
            if ($_SESSION['is-logged-in'] == false) {
                if (isset($_POST['first-name'], $_POST['last-name'], $_POST['email'], $_POST['password'])) {
                    if (!empty($_POST['first-name'])) {
                        if (!empty($_POST['last-name'])) {
                            if (!empty($_POST['email'])) {
                                if (!empty($_POST['password'])) {
                                    if (register_regexCheck($_POST['password'])) {
                                        if (db_registerEmailCheck($_POST['email'])) {
                                            if (db_doRegister($_POST['first-name'], $_POST['last-name'], $_POST['email'], $_POST['password']) == true) {
                                                $resp_code = http_response_code(201);
                                                $resp_body = ['register' => true];
                                                ($_SESSION['is-logged-in'] == true);
                                            } else {
                                                $resp_code = http_response_code(400);
                                                $resp_body = ['register' => 'database fail'];
                                            }
                                        } else {
                                            $resp_code = http_response_code(400);
                                            $resp_body = ['register' => 'email already exists']; 
                                        }
                                    } else {
                                        $resp_code = http_response_code(400);
                                        $resp_body = ['register' => 'validity fail'];
                                    }
                                } else {
                                    $resp_code = http_response_code(400);
                                    $resp_body = ['register' => 'password missing'];
                                }
                            } else {
                                $resp_code = http_response_code(400);
                                $resp_body = ['register' => 'email missing'];
                            }
                        } else {
                            $resp_code = http_response_code(400);
                            $resp_body = ['register' => 'last name missing'];
                        }
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['register' => 'first name missing'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['register' => 'post error'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['register' => 'already logged in'];
            }
            break;

        // Login //
        case 'login':
            if (isset($_POST['email'], $_POST['password'])) {
                if (!empty($_POST['email'])) {
                    if (!empty($_POST['password'])) {
                        if (db_login($_POST['email'], $_POST['password'])) {
                            $resp_code = http_response_code(200);
                            $resp_body = ['login' => true];
                        } else {
                            $resp_code = http_response_code(401);
                            $resp_body = ['login' => false];
                            // Don't want to give hacker any info other than false
                        }
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['login' => 'password not provided'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['login' => 'email not provided'];
                }
            } else {
                $resp_code = http_response_code(400);
                $resp_body = ['login' => 'post error'];
            }
            break;
        
        // Login check //
        case 'is-logged-in':
            if (isUserLoggedIn() == true) {
                $resp_code = http_response_code(200);
                $resp_body = ['is-logged-in' => true];
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['is-logged-in' => false];
            }
            break;

        case 'logout':
            doLogout();
            $resp_code = http_response_code(200);
            $resp_body = ['logout' => true];
            break;

        case 'update-reg':
            if ($_SESSION['is-logged-in']) {
                if (isset($_POST['first-name'], $_POST['last-name'], $_POST['email'], $_POST['password'])) {
                    if (!empty($_POST['first-name'])) {
                        if (!empty($_POST['last-name'])) {
                            if (!empty($_POST['email'])) {
                                if (!empty($_POST['password'])) {
                                    if (register_RegexCheck($_POST['password'])) {
                                        if (db_updateReg($_POST['id'], $_POST['first-name'], $_POST['last-name'], $_POST['email'], $_POST['password'])) {
                                            $resp_code = http_response_code(200);
                                            $resp_body = ['update-reg' => true];
                                        } else {
                                            $resp_code = http_response_code(403);
                                            $resp_body = ['update-reg' => 'database fail'];
                                        }
                                    } else {
                                        $resp_code = http_response_code(400);
                                        $resp_body = ['update-reg' => 'validity fail'];
                                    }
                                } else {
                                    $resp_code = http_response_code(400);
                                    $resp_body = ['update-reg' => 'password not provided'];
                                }
                            } else {
                                $resp_code = http_response_code(400);
                                $resp_body = ['update-reg' => 'email not provided'];
                            }
                        } else {
                            $resp_code = http_response_code(400);
                            $resp_body = ['update-reg' => 'last name not provided'];
                        }
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['update-reg' => 'first name not provided'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['update-reg' => 'post error'];
                }
            } else {
                $resp_code = http_response_code(400);
                $resp_body = ['update-reg' => 'not logged in'];
            }
            break;
        case 'get-category':
            $res = db_getCategory($_GET['id']);
            if (is_array($res)) {
                $resp_code = http_response_code(200);
                $resp_body = ['get-category' => true];
                echo json_encode($res);
            } else {
                $resp_code = http_response_code(400);
                $resp_body = ['get-category' => 'array error'];
            }               
            break;
        case 'select-all-categories':
            $res = db_select_all_categories();
            if (is_array($res)) {
                http_response_code(200);
                echo json_encode($res);
            } else {
                http_response_code(404);
            }               
            break;
        case 'delete-animal':
            if (db_delete($_GET['id']) == true) {
                http_response_code(202);
            } else {
                http_response_code(400);
            }               
            break;
        case 'add-animal':
            if (isUserLoggedIn() == true) {
                if (isset($_GET['category'])) {
                    if (isset($_GET['species']) xor (isset($_POST['species']))) {
                        if (isset($_POST['animal'])) {
                            $resp_code = http_response_code(200);
                            $resp_body = ['add-animal' => true];
                        } else {
                            $resp_code = http_response_code(400);
                            $resp_body = ['add-animal' => 'no animal provided'];
                        }
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['add-animal' => 'no species selected'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['add-animal' => 'no category selected'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['add-animal' => 'unauthorised'];
            }
            break;
        case 'add-log':
            if (isUserLoggedIn() == true) {
                if (isset($_GET['animal']) xor (isset($_POST['animal']))) {
                    if (isset($_POST['log'])) {
                        $resp_code = http_response_code(200);
                        $resp_body = ['add-log' => true];
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['add-log' => 'no log provided'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['add-log' => 'no animal selected'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['add-log' => 'unauthorised'];
            }
            break;
        case 'add-species':
            if (isUserLoggedIn() == true) {
                if (isset($_POST['species'])) {
                    $resp_code = http_response_code(200);
                    $resp_body = ['add-species' => true];
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['add-species' => 'no species provided'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['add-species' => 'unauthorised'];
            }
            break;
        case 'edit-animal':
            if (isUserLoggedIn() == true) {
                if (isset($_GET['animal'])) {
                    if (isset($_POST['edit-animal'])) {
                        $resp_code = http_response_code(200);
                        $resp_body = ['edit-animal' => true];
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['edit-animal' => 'POST error'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['edit-animal' => 'no animal selected'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['edit-animal' => 'unauthorised'];
            }
            break;
        case 'edit-log':
            if (isUserLoggedIn() == true) {
                if (isset($_GET['log'])) {
                    if (isset($_POST['edit-log'])) {
                        $resp_code = http_response_code(200);
                        $resp_body = ['edit-log' => true];
                    } else {
                        $resp_code = http_response_code(400);
                        $resp_body = ['edit-log' => 'post error'];
                    }
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['edit-log' => 'no log selected'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['edit-log' => 'unauthorised'];
            }
            break;
        case 'view-animal':
            if (isUserLoggedIn() == true) {
                if (isset($_GET['animal'])) {
                    $resp_code = http_response_code(200);
                    $resp_body = ['view-animal' => true];
                } else {
                    $resp_code = http_response_code(400);
                    $resp_body = ['view-animal' => 'no animal selected'];
                }
            } else {
                $resp_code = http_response_code(403);
                $resp_body = ['view-animal' => 'unauthorised'];
            }
 
        }
    }
            
// Functions

// if email == email where user id = 1 row, return true


function register_regexCheck ($password) {
    if (preg_match('/^(?=\P{Ll}*\p{Ll})(?=\P{Lu}*\p{Lu})(?=\P{N}*\p{N})(?=[\p{L}\p{N}]*[^\p{L}\p{N}])[\s\S]{8,}$/', $password)) {
        return true;
    } else {
        return false;
    }
}

function db_registerEmailCheck ($email) {
    global $dbconn;
    $sql = "SELECT email FROM users WHERE email = :e";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':e', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() >= 1) { 
        return false;
    }
    return true;
}

function db_updateRegisterEmailCheck ($email, $id) {
    global $dbconn;
    $sql = "SELECT email FROM users WHERE email = :e and user_id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':e', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() >= 1) { 
        return false;
    }
    return true;
}

function db_doRegister ($firstname, $lastname, $email, $password) {
    global $dbconn;
    $sql = "INSERT INTO users (first_name, last_name, email, pass) 
            VALUES (:fn, :ln, :e, :p)";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':fn', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':ln', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':e', $email, PDO::PARAM_STR);
    $stmt->bindParam(':p', $password, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return true;
    }
    return false;
}

function db_login ($email, $password) {
    global $dbconn;
    $sql = "SELECT * FROM users WHERE email = :e AND pass = :p";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':e', $email, PDO::PARAM_STR);
    $stmt->bindParam(':p', $password, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        $_SESSION['is-logged-in'] = true;
        return true;
    }
    return false;
}

function isUserLoggedIn() {
    if ($_SESSION['is-logged-in'] == true) {
        return true;
    } else {
        return false;
    }
}

function doLogout() {
    $_SESSION['is-logged-in'] = false;
}

function db_updateReg ($id, $firstname, $lastname, $email, $password) {
    global $dbconn;
    $sql = "UPDATE users SET first_name = :fn, last_name = :ln, email = :e, pass = :p WHERE user_id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':fn', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':ln', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':e', $email, PDO::PARAM_STR);
    $stmt->bindParam(':p', $password, PDO::PARAM_STR);
    $stmt->execute();
    return true;
  
}

function db_getCategory ($id) {
    global $dbconn;
    $sql = "SELECT type FROM categories WHERE category_id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

function db_select_all_categories () {
    global $dbconn;
    $sql = "SELECT type FROM categories";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

function db_deleteAnimal ($id) {
    global $dbconn;
    $sql = "DELETE FROM todouser WHERE animal_id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return true;  
    }
    return false;
}

// Templates

function db_insert ($u, $p) {
    global $dbconn;
    $sql = "INSERT INTO todouser (user_name, user_email, user_surname, user_password, user_picture, user_role) 
            VALUES (:un, 'email@email.com', :un, :pass, 'defaultpic.jpg', 'user')";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':un', $u, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $p, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return true;
    }
    return false;
}

function db_update ($id, $u, $p) {
    global $dbconn;
    $sql = "UPDATE todouser 
        SET user_name = :u, user_password = :pass WHERE id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':u', $u, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $p, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return true;
    }
    return false;
}

function db_delete ($id) {
    global $dbconn;
    $sql = "DELETE FROM todouser WHERE id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return true;  
    }
    return false;
}

function db_select_one ($id) {
    global $dbconn;
    $sql = "SELECT * FROM todouser WHERE id = :id";
    $stmt = $dbconn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 1) { 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

function db_select_all () {
    global $dbconn;
    $sql = "SELECT * FROM todouser";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

echo json_encode(http_response_code());
echo json_encode($resp_body);