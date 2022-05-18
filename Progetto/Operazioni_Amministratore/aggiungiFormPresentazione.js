
// METODO PER AGGIUNGGERE FORM PER CREAZIONE TUTORIAL
function aggiungiFormTutorial() {

  //attivo il bottone "CREA"
  document.getElementById("btnCrea").removeAttribute('disabled');

  //creo i campi da aggiungere nel form
  const span = document.getElementById("tipoPres");
  const h1 = document.createElement("h1");
  span.innerHTML = "AGGIUNGI INFORMAZIONI TUTORIAL";
  span.appendChild(h1);

  var newField = document.createElement("input");
  newField.setAttribute('type', 'text');
  newField.setAttribute('name', 'abstract');
  newField.setAttribute('placeholder', 'Abstract [max 500 caratteri] ');
  newField.setAttribute('maxlength', '500');
  newField.setAttribute('required', '')
  insertAfter(span, newField);


  var newField = document.createElement("input");
  newField.setAttribute('type', 'text');
  newField.setAttribute('name', 'titoloTutorial');
  newField.setAttribute('placeholder', 'Titolo Tutorial');
  newField.setAttribute('maxlength', '30');
  newField.setAttribute('required', '')
  insertAfter(span, newField);

  var newField = document.createElement("input");
  newField.setAttribute('type', 'text');
  newField.setAttribute('name', 'tipoPres');
  newField.setAttribute('value', 'TUTORIAL');
  newField.readOnly = true;
  insertAfter(span, newField);


}

function insertAfter(referenceNode, newNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}




// METODO PER AGGIUNGGERE FORM PER CREAZIONE ARTICOLO

function aggiungiFormArticolo() {

  //attivo il bottone "CREA"
  document.getElementById("btnCrea").removeAttribute('disabled')

  //creo i campi da aggiungere nel form
  const span = document.getElementById("tipoPres");
  const h1 = document.createElement("h1");
  span.innerHTML = "AGGIUNGI INFORMAZIONI ARTICOLO";
  span.appendChild(h1);


  var newField = document.createElement("input");
  var spanFile = document.createElement("span");
  spanFile.innerHTML = "SELEZIONA L'ARTICOLO";
  spanFile.appendChild(newField);
  newField.setAttribute('type', 'file');
  newField.setAttribute('name', 'filePDF');
  newField.setAttribute('required', '')
  insertAfter(span, spanFile);

  var newField = document.createElement("input");
  newField.setAttribute('type', 'number');
  newField.setAttribute('name', 'numeroPagine');
  newField.setAttribute('placeholder', 'Numero Pagine');
  newField.setAttribute('min', '1');
  newField.setAttribute('required', '')
  insertAfter(span, newField);

  var newField = document.createElement("input");
  newField.setAttribute('type', 'text');
  newField.setAttribute('name', 'titoloArticolo');
  newField.setAttribute('placeholder', 'Titolo');
  newField.setAttribute('maxlength', '30');
  newField.setAttribute('required', '')
  insertAfter(span, newField);

  var newField = document.createElement("input");
  newField.setAttribute('type', 'text');
  newField.setAttribute('name', 'tipoPres');
  newField.setAttribute('value', 'ARTICOLO');
  newField.readOnly = true;
  insertAfter(span, newField);


}

