const TABLES = [
  'Library_Db','Faculty_Db','Student_Db','Library_Staff_Db','Category_Db','Book_Details_Db',
  'Warehouse_Db','Transactions_Db','Issue_Details_Db','Return_Details_Db','Penalty_Db','Registration_Db'
];

const configCols = {
  'Library_Db':['Library_id','BranchName','Location','LibraryManager','TotalBooks','StaffCount','MemberCount','BooksIssued','Status','Description'],
  'Faculty_Db':['Faculty_id','Library_id','FullName','Rank','DOB','Email','Address','AccountStatus'],
  'Student_Db':['Student_id','Library_id','Faculty_id','FullName','Rank','DOB','Email','City','Address','ContactNumber'],
  'Library_Staff_Db':['Staff_id','Library_id','FirstName','LastName','Email','Position','HireDate','ShiftTime','UserName','Password','Status','Phone'],
  'Category_Db':['Category_id','CategoryName','Description'],
  'Book_Details_Db':['Book_id','Library_id','Category_id','PublisherName','AuthorName','BookName','Edition','PageCount','Description','CopyCount','Status'],
  'Warehouse_Db':['Storage_id','Library_id','Book_id','Location','ShelfNumber','Quantity','CurrentLoad','Status'],
  'Transactions_Db':['Transaction_id','Faculty_id','Student_id','Book_id','IssueDate','ReturnDate','IssueBy','ReceiveBy','DueDate','Status','Note'],
  'Issue_Details_Db':['Issue_id','Student_id','Book_id','Faculty_id','IssueBy','IssueDate','ReturnDate'],
  'Return_Details_Db':['Ret_id','Student_id','Book_id','ReceiveBy','IssueDate','ReturnDate','DueDate'],
  'Penalty_Db':['Penalty_id','Student_id','Return_id','Amount','PenaltyDate','PaidStatus','DueDays'],
  'Registration_Db':['ID','Student_id','UserName','Password','Description']
};

let currentTable = TABLES[0];

document.addEventListener('DOMContentLoaded', () => {
  const tableSelector = document.getElementById('tableSelector');
  const topnav = document.getElementById('topnav');

  // ساخت منو در دو ردیف
  const row1 = document.createElement('div'); row1.className = 'nav-row';
  const row2 = document.createElement('div'); row2.className = 'nav-row';
  TABLES.forEach((t,i) => {
    const btn = document.createElement('button');
    btn.textContent = t; btn.className='nav-btn';
    btn.onclick = ()=> { selectTable(t); tableSelector.value=t; };
    if(i<6) row1.appendChild(btn); else row2.appendChild(btn);
  });
  topnav.appendChild(row1); topnav.appendChild(row2);

  // ساخت select options
  TABLES.forEach(t => {
    const opt = document.createElement('option'); opt.value=t; opt.textContent=t;
    tableSelector.appendChild(opt);
  });
  tableSelector.onchange = ()=> selectTable(tableSelector.value);

  // دکمه‌ها و input
  document.getElementById('btnRefresh').onclick = ()=> loadData();
  document.getElementById('btnAdd').onclick = ()=> openModal('add');
  document.getElementById('cancelBtn').onclick = closeModal;
  document.getElementById('saveBtn').onclick = saveEntity;
  document.getElementById('globalSearch').oninput = filterTable;

  selectTable(currentTable);
});

function selectTable(t){
  currentTable=t;
  document.getElementById('modalTitle').textContent=t;
  loadData();
}

async function loadData(){
  showFeedback('Loading...');
  const res = await fetch(`api.php?action=list&table=${encodeURIComponent(currentTable)}`);
  const data = await res.json();
  if(!data.success){ showFeedback('Load error: '+(data.message||'')); return; }
  renderTable(data.data);
  showFeedback('Loaded: '+(data.data.length||0)+' rows');
}

function renderTable(rows){
  const head=document.getElementById('tableHead');
  const body=document.getElementById('tableBody');
  head.innerHTML=''; body.innerHTML='';

  const cols=configCols[currentTable];
  const trh=document.createElement('tr');
  cols.forEach(c=>{ const th=document.createElement('th'); th.textContent=c; trh.appendChild(th); });
  const thAction=document.createElement('th'); thAction.textContent='Actions'; trh.appendChild(thAction);
  head.appendChild(trh);

  rows.forEach(r=>{
    const tr=document.createElement('tr');
    cols.forEach(c=>{ const td=document.createElement('td'); td.textContent=r[c]??''; tr.appendChild(td); });
    const tdAct=document.createElement('td');
    const btnEdit=document.createElement('button'); btnEdit.textContent='Edit'; btnEdit.onclick=()=>openModal('edit',r);
    const btnDel=document.createElement('button'); btnDel.textContent='Delete'; btnDel.onclick=()=>deleteEntity(r);
    tdAct.appendChild(btnEdit); tdAct.appendChild(btnDel); tr.appendChild(tdAct);
    body.appendChild(tr);
  });
}

function openModal(mode='add', row=null){
  const modal=document.getElementById('modal');
  const form=document.getElementById('entityForm');
  form.innerHTML=''; form.dataset.mode=mode;
  const cols=configCols[currentTable];
  cols.forEach(c=>{
    const field=document.createElement('div'); field.className='form-row';
    const label=document.createElement('label'); label.textContent=c;
    let input;
    if(mode==='edit' && c===cols[0]){ input=document.createElement('input'); input.type='text'; input.name=c; input.value=row[c]??''; input.readOnly=true; }
    else {
      if(c.toLowerCase().includes('date')) input=document.createElement('input'), input.type='date';
      else if(c.toLowerCase().includes('amount')||c.toLowerCase().includes('count')||c.toLowerCase().includes('id')||c.toLowerCase().includes('number')||c.toLowerCase().includes('quantity')||c.toLowerCase().includes('page')) input=document.createElement('input'), input.type='number';
      else input=document.createElement('input'), input.type='text';
      input.name=c; input.value=row? row[c]??'':'';
    }
    field.appendChild(label); field.appendChild(input); form.appendChild(field);
  });
  modal.classList.remove('hidden');
}

function closeModal(){ document.getElementById('modal').classList.add('hidden'); }

async function saveEntity(){
  const form=document.getElementById('entityForm');
  const mode=form.dataset.mode;
  const cols=configCols[currentTable];
  const data=new FormData();
  data.append('action',mode==='add'?'add':'update');
  data.append('table',currentTable);
  cols.forEach(c=>{ const el=form.querySelector(`[name="${c}"]`); if(el) data.append(c,el.value); });
  const res=await fetch('api.php',{method:'POST',body:data});
  const j=await res.json();
  alert(j.message); if(j.success){ closeModal(); loadData(); }
}

async function deleteEntity(row){
  const cols=configCols[currentTable];
  const idcol=cols[0];
  if(!confirm('Are you sure to delete ID='+row[idcol]+' ?')) return;
  const fd=new FormData(); fd.append('action','delete'); fd.append('table',currentTable); fd.append(idcol,row[idcol]);
  const res=await fetch('api.php',{method:'POST',body:fd});
  const j=await res.json(); alert(j.message); if(j.success) loadData();
}

function filterTable(ev){
  const q=ev.target.value.toLowerCase();
  document.querySelectorAll('#tableBody tr').forEach(tr=>{ tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none'; });
}

function showFeedback(msg){ document.getElementById('feedback').textContent=msg; }
