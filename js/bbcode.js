
function insertTag(startTag, endTag, textareaId, tagType) {
        var field  = document.getElementById(textareaId); 
        var scroll = field.scrollTop;
        field.focus();
        
        /* === Partie 1 : on récupère la sélection === */
        if (window.ActiveXObject) {
                var textRange = document.selection.createRange();            
                var currentSelection = textRange.text;
        } else {
                var startSelection   = field.value.substring(0, field.selectionStart);
                var currentSelection = field.value.substring(field.selectionStart, field.selectionEnd);
                var endSelection     = field.value.substring(field.selectionEnd);               
        }
        
        /* === Partie 2 : on analyse le tagType === */
        if (tagType) {
                switch (tagType) {
                     
                        case "lien":
				endTag = "</lien>";
				if (currentSelection) { // Il y a une sélection
					if (currentSelection.indexOf("http://") == 0 || currentSelection.indexOf("https://") == 0 || currentSelection.indexOf("ftp://") == 0 || currentSelection.indexOf("www.") == 0) {
						// La sélection semble être un lien. On demande alors le libellé
						var label = prompt("Quel est le libellé du lien ?") || "";
						startTag = "<lien url=\"" + currentSelection + "\">";
						currentSelection = label;
					} else {
						// La sélection n'est pas un lien, donc c'est le libelle. On demande alors l'URL
						var URL = prompt("Quelle est l'url ?");
						startTag = "<lien url=\"" + URL + "\">";
					}
				} else { // Pas de sélection, donc on demande l'URL et le libelle
					var URL = prompt("Quelle est l'url ?") || "";
					var label = prompt("Quel est le libellé du lien ?") || "";
					startTag = "<lien url=\"" + URL + "\">";
					currentSelection = label;                     
				}
			break;

                                
                                
                
                        case "citation":
				endTag = "</citation>";
				if (currentSelection) { // Il y a une sélection
					if (currentSelection.length > 30) { // La longueur de la sélection est plus grande que 30. C'est certainement la citation, le pseudo fait rarement 20 caractères
						var auteur = prompt("Quel est l'auteur de la citation ?") || "";
						startTag = "<citation nom=\"" + auteur + "\">";
					} else { // On a l'Auteur, on demande la citation
						var citation = prompt("Quelle est la citation ?") || "";
						startTag = "<citation nom=\"" + currentSelection + "\">";
						currentSelection = citation;    
					}
				} else { // Pas de selection, donc on demande l'Auteur et la Citation
					var auteur = prompt("Quel est l'auteur de la citation ?") || "";
					var citation = prompt("Quelle est la citation ?") || "";
					startTag = "<citation nom=\"" + auteur + "\">";
					currentSelection = citation;    
				}
			break;

                }
        }
        
        /* === Partie 3 : on insère le tout === */
        if (window.ActiveXObject) {
                textRange.text = startTag + currentSelection + endTag;
                textRange.moveStart("character", -endTag.length - currentSelection.length);
                textRange.moveEnd("character", -endTag.length);
                textRange.select();     
        } else {
                field.value = startSelection + startTag + currentSelection + endTag + endSelection;
                field.focus();
                field.setSelectionRange(startSelection.length + startTag.length, startSelection.length + startTag.length + currentSelection.length);
        } 

        field.scrollTop = scroll;     
}


