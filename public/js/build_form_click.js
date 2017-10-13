/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function buildForm(action){
    var form='<form id="edit-form" method="POST" action="'+action+'">'+
           '<input type="hidden" value="" name="id"/>'+
        '</form>';
    jQuery("body").append(form);
    
}


