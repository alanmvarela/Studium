/**
 * @license
 * Copyright 2012 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @fileoverview Loading and saving blocks with localStorage and cloud storage.
 * @author q.neutron@gmail.com (Quynh Neutron)
 */
'use strict';


// Create a namespace.
var BlocklyStorage = {};

/**
* Save blocks or JavaScript to Studium database.
*
* @author Alan Varela
*/
BlocklyStorage.link = function() {

  var url = window.location.search,
      urlParameters = (url.substring(9, url.length)).split('&');

  //IF THERE ARE PARAMETERS ON THE URL THEN A STUDENT IS EXECUTING THE GAME. OTHERWISE A TEACHER IS CORRECTING THE GAME.
  if (urlParameters.length > 2){
    var code = BlocklyInterface.getCode(),
        soapBody = BlocklyStorage.getSoapWSSaveBlocklyAttemptBody(encodeURIComponent(code));

    BlocklyStorage.soapRequest(soapBody, BlocklyStorage.handleSoapWSSaveBlocklyAttemptResponse);
  } else {
    window.localStorage.clear();
    window.opener.focus();
    window.close();
  }
};

/**
 * Retrieve XML text from url and restores the current game status.
 * @param {string} code Encoded XML obtained from href containing the saved status of a game.
 *
 * @author Alan Varela
 */
BlocklyStorage.retrieveXml = function(code) {
  BlocklyInterface.setCode(decodeURIComponent(code));
};

/**
 * Bind the link function to the unload event.
 *
 * @author Alan Varela
 */
BlocklyStorage.backupOnUnload = function() {
  window.addEventListener('unload', BlocklyStorage.link, false);
};

/**
 * Creates a SOAP request addresed to the Studium webservice.
 *
 * @param {string} soapBody contains the soap envelop body with the method and parameters to be used on the request.
 *
 * @author Alan Varela
 */
BlocklyStorage.soapRequest = function( soapBody, callBackFunction ) {
    var xmlhttp = new XMLHttpRequest();
    var ws_url = window.location.protocol +'//' + window.location.hostname + '/main/webservices/blockly-games.soap.php?wsdl';

    xmlhttp.open('POST', ws_url, true);
    
    // build SOAP request
    var sr =
        '<?xml version="1.0" encoding="utf-8"?>' +
        '<soapenv:Envelope ' +
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' +
            'xmlns:api="http://127.0.0.1/Integrics/Enswitch/API" ' +
            'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ' +
            'xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">' +
            '<soapenv:Body>' +
              soapBody +
            '</soapenv:Body>' +
        '</soapenv:Envelope>';

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
          callBackFunction(xmlhttp.status);
        }
    }
    // Send the POST request
    xmlhttp.setRequestHeader('Content-Type', 'text/xml');
    xmlhttp.send(sr);
}

/**
 * Returs the soap envelop body for the Studium xxx webservice method with all its parameters
 *
 * @param {string} code contains the status of the current BLOCKLY-GAMES.
 *
 * @author Alan Varela
 */
BlocklyStorage.getSoapWSSaveBlocklyAttemptBody = function ( code ) {

  var method = "WSSaveBlocklyAttempt",
      soapBody =  '<' + method + ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' +
                    '<blocklyAttempt>'+
                      BlocklyStorage.getSoapParameters() +
                      '<choice xsi:type="xsd:string">' + code + '</choice>' +
                    '</blocklyAttempt>'+
                  '</' + method + '>';
  return soapBody;
}

/**
 * Handles the response of the SOAP request invoquing WSSaveBlocklyAttempt method.
 *
 * @param {string} status contains the status code of the SOAP request.
 *
 * @author Alan Varela
 */
BlocklyStorage.handleSoapWSSaveBlocklyAttemptResponse = function ( status ) {
  if (status == 200) {
      BlocklyStorage.alert("El juego fue guardado con exito.");
  } else {
      window.alert("Hubo un error al guardar el juego.");
  }
}

/**
 * Returns all the parameters on the current window URL querystring formated to be included in a Studium SOAP request.
 * choiche[d] parameter is ommited from the return string.
 *
 * @author Alan Varela
 */
BlocklyStorage.getSoapParameters = function () {
  var url = window.location.search,
  //REMOVES LANGUAGE PARAMETERS AND ? CHARACTER FROM BLOCKLY-GAMES URL.
      urlParameters = (url.substring(9, url.length)).split('&'),
      currentParameter,
      currentParameterName,
      currentParameterValue,
      i,
      soapParameters = "";

  for (i = 0; i < urlParameters.length; i++) {
      currentParameter = urlParameters[i].split('=');
      //REMOVES choiche[*] and currentUrl PARAMETERS FROM THE PARAMETERS LIST OBTAINED FROM BLOCKLY_GAMES URL.
      currentParameterName = decodeURIComponent(currentParameter[0]).replace(/choice\[\d*\]/, 'choice');
      if (currentParameter[0] != '' && currentParameterName != 'choice' && currentParameterName != 'currentUrl') {
        currentParameterValue = decodeURIComponent(currentParameter[1]);
        soapParameters = soapParameters + '<' + currentParameterName + ' xsi:type="xsd:string">' + currentParameterValue + '</' + currentParameterName + '>'
      }
  }

  return soapParameters;
}

/**
 * Present a text message to the user.
 * Designed to be overridden if an app has custom dialogs, or a butter bar.
 * @param {string} message Text to alert.
 */
BlocklyStorage.alert = function(message) {
  window.alert(message);
};

//ALL THE STORAGE METHODS FROM BLOCKLY-GAMES WHERE COMMENTED OUT AS THEY WORK WITH APPENGINE OR LOCALSTORAGE.

/**
 * Backup code blocks or JavaScript to localStorage.
 * @private
 */
/*
BlocklyStorage.backupBlocks_ = function() {
  if ('localStorage' in window) {
    var code = BlocklyInterface.getCode();
    // Gets the current URL, not including the hash.
    var url = window.location.href.split('#')[0];
    window.localStorage.setItem(url, code);
  }
};
*/

/**
 * Restore code blocks or JavaScript from localStorage.
 */
/*
BlocklyStorage.restoreBlocks = function() {
  var url = window.location.href.split('#')[0];
  if ('localStorage' in window && window.localStorage[url]) {
    var code = window.localStorage[url];
    BlocklyInterface.setCode(code);
  }
};
*/

/**
 * Global reference to current AJAX requests.
 * @type Object.<string, XMLHttpRequest>
 */
/*
BlocklyStorage.xhrs_ = {};
*/

/**
 * Fire a new AJAX request.
 * @param {string} url URL to fetch.
 * @param {string} data Body of data to be sent in request.
 * @param {?Function=} opt_onSuccess Function to call after request completes
 *    successfully.
 * @param {?Function=} opt_onFailure Function to call after request completes
 *    unsuccessfully. Defaults to BlocklyStorage alert of request status.
 * @param {string=} [opt_method='POST'] The HTTP request method to use.
 */
/*
BlocklyStorage.makeRequest =
    function(url, data, opt_onSuccess, opt_onFailure, opt_method) {
  if (BlocklyStorage.xhrs_[url]) {
    // AJAX call is in-flight.
    BlocklyStorage.xhrs_[url].abort();
  }
  BlocklyStorage.xhrs_[url] = new XMLHttpRequest();
  BlocklyStorage.xhrs_[url].onload = function() {
    if (this.status === 200) {
      opt_onSuccess && opt_onSuccess.call(this);
    } else if (opt_onFailure) {
      opt_onFailure.call(this);
    } else {
      BlocklyStorage.alert(BlocklyStorage.HTTPREQUEST_ERROR + '\n' +
          'xhr_.status: ' + this.status);
    }
    BlocklyStorage.xhrs_[url] = null;
  };
  var method = opt_method || 'POST';
  BlocklyStorage.xhrs_[url].open(method, url);
  if (method === 'POST') {
    BlocklyStorage.xhrs_[url].setRequestHeader('Content-Type',
        'application/x-www-form-urlencoded');
  }
  BlocklyStorage.xhrs_[url].send(data);
};
*/

/**
 * Callback function for link AJAX call.
 * @param {string} responseText Response to request.
 * @private
 */
/*
/**
 * Callback function for retrieve xml AJAX call.
 * @param {string} responseText Response to request.
 * @private
 */
/*
BlocklyStorage.handleRetrieveXmlResponse_ = function() {
  var data = this.responseText.trim();
  if (!data.length) {
    BlocklyStorage.alert(BlocklyStorage.HASH_ERROR.replace('%1',
        window.location.hash));
  } else {
    BlocklyInterface.setCode(data);
  }
  BlocklyStorage.monitorChanges_();
};
*/

/**
 * Start monitoring the workspace.  If a change is made that changes the XML,
 * clear the key from the URL.  Stop monitoring the workspace once such a
 * change is detected.
 * @private
 */
/*
BlocklyStorage.monitorChanges_ = function() {
  var startCode = BlocklyInterface.getCode();
  function change() {
    if (startCode != BlocklyInterface.getCode()) {
      window.location.hash = '';
      BlocklyInterface.getWorkspace().removeChangeListener(bindData);
    }
  }
  var bindData = BlocklyInterface.getWorkspace().addChangeListener(change);
};
*/
