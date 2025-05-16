function formToggle(ID) {
    var element = document.getElementById(ID);
    if(element.style.display === "none") {
        element.style.display = "block";
    }else {
        element.style.display = "none";
    }
}

var selectObj = {
    "India" : { "Gujarat" : [ "Ahmedabad", "Gandhinagar", "Rajkot", "Surat"],
        "Goa" : [ "Panaji", "Margao", "Vasco da Gama", "Ponda"],
        "Kerala" : [ "Thiruvananthapuram", "Kochi", "Kollam", "Kozhikode"],
        "Punjab" : [ "Amritsar", "Chandigarh", "jalandhar", "Patiala"],
    },
    "Australia" : { "Victoria" : [ "Geelong", "Ballarat", "Bendigo"],
        "Western Australia" : [ "Perth", "Bunbury", "Geraldton"],
        "South Australia" : [ "Adelaide", "Mount Gambier"],
    },
    "Brazil" : { "Sao Paulo" : [ "Campinas", "Osasco"],
        "Distrito Federal" : [ "Milpa Alta", "Iztacalco"],
    },
    "Canada" : { "Ontario" : [ "Toronto", "Ottawa"],
        "Alberta" : [ "Calgary", "Edmonton"],
        "Manitoba" : [ "Brandon", "Winnipeg"],
    },
    "Denmark" : { "Zeeland" : [ "Veere", "Goes"],
        "Funen" : [ "Odense", "Nyborg"],
        "Greenland" : [ "Nuuk", "Sisimiut"],
    }
}

function selectFun() {
    var countrySel = document.getElementById("country");
    var stateSel = document.getElementById("state");
    var citySel = document.getElementById("city");

    for(var c in selectObj) {
        countrySel.options[countrySel.options.length] = new Option(c, c);
    }
    countrySel.onchange = function(){
        stateSel.length = 1;    //remove all options bar first
        citySel.length = 1;     //remove all options bar first

        if(this.selectedIndex < 1)  return;     //done

        for(var s in selectObj[this.value]){
            stateSel.options[stateSel.options.length] = new Option(s, s);
        }
    }
    countrySel.onchange();      // reset in case page is reloaded
    stateSel.onchange = function() {
        citySel.length = 1;

        if(this.selectedIndex < 1)  return;

        var cty = selectObj[countrySel.value][this.value];
        for(var i = 0; i < cty.length; i++){
            citySel.options[citySel.options.length] = new Option(cty[i], cty[i]);
        }
    }
}