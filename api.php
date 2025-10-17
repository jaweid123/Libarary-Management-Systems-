<?php
// api.php
header('Content-Type: application/json; charset=utf-8');
require_once 'connection.php';

// whitelist جدول‌ها + ستون‌ها + نام ستون ID برای هر جدول
$TABLES = [
    'Library_Db' => ['cols'=>['Library_id','BranchName','Location','LibraryManager','TotalBooks','StaffCount','MemberCount','BooksIssued','Status','Description'],'id'=>'Library_id'],
    'Faculty_Db' => ['cols'=>['Faculty_id','Library_id','FullName','Rank','DOB','Email','Address','AccountStatus'],'id'=>'Faculty_id'],
    'Student_Db' => ['cols'=>['Student_id','Library_id','Faculty_id','FullName','Rank','DOB','Email','City','Address','ContactNumber'],'id'=>'Student_id'],
    'Library_Staff_Db' => ['cols'=>['Staff_id','Library_id','FirstName','LastName','Email','Position','HireDate','ShiftTime','UserName','Password','Status','Phone'],'id'=>'Staff_id'],
    'Category_Db' => ['cols'=>['Category_id','CategoryName','Description'],'id'=>'Category_id'],
    'Book_Details_Db' => ['cols'=>['Book_id','Library_id','Category_id','PublisherName','AuthorName','BookName','Edition','PageCount','Description','CopyCount','Status'],'id'=>'Book_id'],
    'Warehouse_Db' => ['cols'=>['Storage_id','Library_id','Book_id','Location','ShelfNumber','Quantity','CurrentLoad','Status'],'id'=>'Storage_id'],
    'Transactions_Db' => ['cols'=>['Transaction_id','Faculty_id','Student_id','Book_id','IssueDate','ReturnDate','IssueBy','ReceiveBy','DueDate','Status','Note'],'id'=>'Transaction_id'],
    'Issue_Details_Db' => ['cols'=>['Issue_id','Student_id','Book_id','Faculty_id','IssueBy','IssueDate','ReturnDate'],'id'=>'Issue_id'],
    'Return_Details_Db' => ['cols'=>['Ret_id','Student_id','Book_id','ReceiveBy','IssueDate','ReturnDate','DueDate'],'id'=>'Ret_id'],
    'Penalty_Db' => ['cols'=>['Penalty_id','Student_id','Return_id','Amount','PenaltyDate','PaidStatus','DueDays'],'id'=>'Penalty_id'],
    'Registration_Db' => ['cols'=>['ID','Student_id','UserName','Password','Description'],'id'=>'ID'],
];

// helper
function bad($msg){ echo json_encode(['success'=>false,'message'=>$msg]); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';
$table = $_REQUEST['table'] ?? '';

// validate table
if (!isset($TABLES[$table])) bad('Invalid table');

// columns metadata
$meta = $TABLES[$table];
$idcol = $meta['id'];
$cols = $meta['cols'];

// === LIST ===
if ($action === 'list' && $method === 'GET') {
    // optional pagination/filter could be added
    $sql = "SELECT " . implode(',', $cols) . " FROM [$table] ORDER BY $idcol DESC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true,'data'=>$rows]);
    exit;
}

// === ADD ===
if ($action === 'add' && $method === 'POST') {
    // read posted fields for allowed columns (exclude id)
    $insertCols = array_filter($cols, fn($c)=> $c !== $idcol);
    $data = [];
    foreach ($insertCols as $c) {
        // accept empty string as NULL for date/int? keep as is
        $data[$c] = array_key_exists($c, $_POST) ? ($_POST[$c] === '' ? null : $_POST[$c]) : null;
    }
    // build statement
    $fields = array_filter($insertCols, fn($c)=> isset($data[$c])); // keep all columns for simplicity
    $placeholders = array_map(fn($c)=>':'.$c, $fields);
    if (count($fields) === 0) bad('No data to insert');
    $sql = "INSERT INTO [$table] (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    foreach ($fields as $f) $stmt->bindValue(':'.$f, $data[$f]);
    try {
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Inserted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Insert failed: '.$e->getMessage()]);
    }
    exit;
}

// === UPDATE ===
if ($action === 'update' && $method === 'POST') {
    $id = $_POST[$idcol] ?? null;
    if (!$id) bad('ID required for update');
    $updateCols = array_filter($cols, fn($c)=> $c !== $idcol);
    $setParts = [];
    $params = [];
    foreach ($updateCols as $c) {
        if (array_key_exists($c, $_POST)) {
            $setParts[] = "[$c] = :$c";
            $params[$c] = $_POST[$c] === '' ? null : $_POST[$c];
        }
    }
    if (count($setParts) === 0) bad('No fields to update');
    $sql = "UPDATE [$table] SET " . implode(', ', $setParts) . " WHERE [$idcol] = :_id";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k=>$v) $stmt->bindValue(':'.$k, $v);
    $stmt->bindValue(':_id', $id);
    try {
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Update failed: '.$e->getMessage()]);
    }
    exit;
}

// === DELETE ===
if ($action === 'delete' && $method === 'POST') {
    $id = $_POST[$idcol] ?? null;
    if (!$id) bad('ID required for delete');
    $sql = "DELETE FROM [$table] WHERE [$idcol] = :_id";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([':_id'=>$id]);
        echo json_encode(['success'=>true,'message'=>'Deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Delete failed: '.$e->getMessage()]);
    }
    exit;
}

bad('Action not supported');
