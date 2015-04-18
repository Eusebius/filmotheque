/**
 * includes/test.js
 * 
 * @author Eusebius <eusebius@eusebius.fr>
 * @since 0.2.4
 * 
 * This is a test JavaScript file providing AJAX capabilities to forms.
 */
/*
    Filmothèque
    Copyright (C) 2012-2013 Eusebius (eusebius@eusebius.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
// retourne un objet xmlHttpRequest.
// méthode compatible entre tous les navigateurs (IE/Firefox/Opera)
function getXMLHTTP(){
  var xhr=null;
  if(window.XMLHttpRequest) // Firefox et autres
  xhr = new XMLHttpRequest();
  else if(window.ActiveXObject){ // Internet Explorer
    try {
      xhr = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e1) {
        xhr = null;
      }
    }
  }
  else { // XMLHttpRequest non supporté par le navigateur
    alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
  }
  return xhr;
}

var _documentForm=null; // le formulaire contenant notre champ texte
var _inputField=null; // le champ texte lui-même
var _submitButton=null; // le bouton submit de notre formulaire

function initAutoComplete(form,field,submit){
  _documentForm=form;
  _inputField=field;
  _submitButton=submit;
  _inputField.autocomplete="off";
  creeAutocompletionListe();
  _currentInputFieldValue=_inputField.value;
  _oldInputFieldValue=_currentInputFieldValue;
  cacheResults("",new Array())
  // Premier déclenchement de la fonction dans 200 millisecondes
  setTimeout("mainLoop()",200)
}

var _oldInputFieldValue=""; // valeur précédente du champ texte
var _currentInputFieldValue=""; // valeur actuelle du champ texte
var _resultCache=new Object(); // mécanisme de cache des requetes

// tourne en permanence pour suggerer suite à un changement du champ texte
function mainLoop(){
  _currentInputFieldValue = _inputField.value;
  if(_oldInputFieldValue!=_currentInputFieldValue){
    var valeur=escapeURI(_currentInputFieldValue);
    var suggestions=_resultCache[_currentInputFieldValue];
    if(suggestions){ // la réponse était encore dans le cache
      metsEnPlace(valeur,suggestions)
    }else{
      callSuggestions(valeur) // appel distant
    }
    _inputField.focus()
  }
  _oldInputFieldValue=_currentInputFieldValue;
  setTimeout("mainLoop()",200); // la fonction se redéclenchera dans 200 ms
  return true
}

// echappe les caractère spéciaux
function escapeURI(La){
  if(encodeURIComponent) {
    return encodeURIComponent(La);
  }
  if(escape) {
    return escape(La)
  }
}

var _xmlHttp = null; //l'objet xmlHttpRequest utilisé pour contacter le serveur
var _adresseRecherche = "scripts/suggestPersons.php" //l'adresse à interroger pour trouver les suggestions

function callSuggestions(valeur){
  if(_xmlHttp&&_xmlHttp.readyState!=0){
    _xmlHttp.abort()
  }
  _xmlHttp=getXMLHTTP();
  if(_xmlHttp){
    //appel à l'url distante
    _xmlHttp.open("GET",_adresseRecherche+"?prefix="+valeur,true);
    _xmlHttp.onreadystatechange=function() {
      if(_xmlHttp.readyState==4&&_xmlHttp.responseXML) {
        var liste = traiteXmlSuggestions(_xmlHttp.responseXML)
        cacheResults(valeur,liste)
        metsEnPlace(valeur,liste)
      }
    };
    // envoi de la requete
    _xmlHttp.send(null)
  }
}

// Mecanisme de caching des réponses
function cacheResults(debut,suggestions){
  _resultCache[debut]=suggestions
}

// Transformation XML en tableau
function traiteXmlSuggestions(xmlDoc) {
  var options = xmlDoc.getElementsByTagName('option');
  var optionsListe = new Array();
  for (var i=0; i < options.length; ++i) {
    optionsListe.push(options[i].firstChild.data);
  }
  return optionsListe;
}

var _completeListe=null; // la liste contenant les suggestions

// création d'une div pour les suggestions
// méthode appellée à l'initialisation
function creeAutocompletionListe(){
  _completeListe=document.createElement("UL");
  _completeListe.id="completeDiv";
  document.body.appendChild(_completeListe);
}

function metsEnPlace(valeur, liste) {
  while(_completeListe.childNodes.length>0) {
    _completeListe.removeChild(_completeListe.childNodes[0]);
  }
  for (var i=0; i < liste.length; ++i) {
    var nouveauElmt = document.createElement("LI")
    nouveauElmt.innerHTML = liste[i]
    _completeListe.appendChild(nouveauElmt)
  }
}