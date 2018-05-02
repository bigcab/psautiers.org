(define-module (scm cab_to_xml))


(use-modules (ice-9 regex)
	     (srfi srfi-1)
	     (oop goops)
	     (scm xml_class))



(define node-attributes-list
	'(	(score-partwise. (version))
		(part-list. '())
		(score-part . (id))
		(creator .(composer))
		(part . (id))
		(measure . (number width))
		(clef . (number))
		(stem . (default-y))
		(beam . (number))
		(note . (default-x))))


;<note default-x="239">
;        <pitch>
;          <step>F</step>
;          <octave>4</octave>
;        </pitch>
;        <duration>1</duration>
;        <tie type="stop"/>
;        <voice>2</voice>
;        <type>16th</type>
;        <stem default-y="-65">down</stem>
;        <staff>1</staff>
;        <beam number="1">begin</beam>
;        <beam number="2">begin</beam>
;        <notations>
;          <tied type="stop"/>
;        </notations>
;      </note>


;; this list tells us what to do when we encounter an event,pitch etc ...


(define ly-xml-list
	'(	(RelativeOctaveMusic . part)
		(SequentialMusic . part)
		(NoteEvent . note)
		(pitch . pitch)
		(duration . duration)
		(octave . octave)
		(step . step)))
		
;; this tells us whether we have to call get_elements_with_property or get_property		
(define properties-list 
    '(pitch))

;;works
(define (is_property name)
    (memq name properties-list))        
;; this list tells you which function to call to retrieve the right scheme object  , there's fun and fun?
;; i'll just have to call get_elements_with property or get_properties
;; syntax (duration . (fun fun?))
(define retrieve-object-function-list
    `((duration .   (,(lambda(x) (ly:music-property x 'duration)) ,ly:duration?))
    (NoteEvent  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'NoteEvent))))
    (pitch      .   (,(lambda(x) (ly:music-property x 'pitch)) ,ly:pitch?))
    (SequentialMusic  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'SequentialMusic))))
    (RelativeOctaveMusic  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'RelativeOctaveMusic))))
    (OverrideProperty  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'OverrideProperty))))
    (PropertySet  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'PropertySet))))
    (ContextSpeccedMusic  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'ContextSpeccedMusic))))
    (SimultaneousMusic  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'SimultaneousMusic))))
    (EventChord  .   (,(lambda(x) (ly:music-property x 'name)) ,(lambda(x) (eq? x 'EventChord))))
    (step   . (,(lambda(x) (ly:music-property x 'pitch)) ,ly:pitch?))
    (octave . (,(lambda(x) (ly:music-property x 'pitch)) ,ly:pitch?))))

;; once you retrived the good object you just have to extract its value
;; functions to retrieve the value of duration/step/octave
(define function-value-list
        `((duration      . (,ly:duration-log ))
        (step           . (,ly:pitch-notename ))
        (octave         . (,ly:pitch-octave ))))
    
;; return false if value is not found
(define (get_value music name )
    (if (assq name function-value-list)
        (let (fun (cadr(assq name function-value-list)))
            (fun music))
        (#f)))

;works
(define (translate_to_xml  name )
    (cdr(assq name ly-xml-list )))


;(define (parse_elem name music)
;    (let (callbacks (assq name retrieve-object-function-list))
;        (if (pair? callbacks)
;            (let ((fun (car callbacks))
;                (fun? (car(cdr callbacks))))
;                (if (memq name properties-list)
;                    (let ( obj (get_properties music fun fun?))
;                        ())
;                    ()))
;            '()))) 
;; pp is pitch object
(define (parse_step pp)
    (make-xml-node 'step '() (list (make-inline-text (get_value pp 'step)))))
(define (parse_octave pp)
    (make-xml-node 'octave '() (list (make-inline-text (get_value pp 'octave)))))
(define (parse_pitch music)
    (let* (  (fun (cadr (assq 'pitch retrieve-object-function-list))
            (fun? (caddr (cdr( assq 'pitch retrieve-object-function-list))))
            (obj (car (get_properties music fun fun?))))
            (xml_name (cdr(assq 'pitch ly-xml-list)))
            (att (assq xml_name node-attributes-list))
            (children (assq xml_name node-children-list)))
        (make-xml-node 'pitch '() (list (parse_octave obj ) (parse_step obj)))))

(define node-children-list
	'(	(identification . (creator encoding))
		(defaults . (scaling page-layout system-layout staff-layout appearance music-font word-font))
		(part-list . (score-part))
		(score-part . (part-name score-instrument midi-instrument))
		(score-instrument . (instrument-name))
		(midi-instrument . (midi-channel midi-program))	
		(part . (measure))
		(measure .(print attributes direction note backup forward) )
		(print. ())
		(attributes. (divisions key time clef staves))
		(key. (fifths mode))
		(time. (beats beat-type))
		(clef. (sign line))
		(note . (pitch duration tie voice type stem staff beam notations))
		(pitch . (step octave))
		(notations . (tied))))




;;example to test if everything works well

(define inline (make-inline-text "cool"))
(define note (make-xml-node "note" '() (list inline)))
(define att (make-attribute "test" "ca marche"))
(define score (make-xml-node "score" (list att) (list note) ))



(define (affich_arbre music)
	((display (ly:music-property music 'name))
	(display "\n")
	(for-each affich_arbre (ly:music-property music 'elements))
	))
;;(display (xml-display score 0))

(define (disp u) (begin (display  u) (display "\n")))

(define (get_element music) 
	(ly:music-property music 'element))
(define (get_elements music)
	(ly:music-property music 'elements))
(define (get_name music)
	(ly:music-property music 'name))

(define (get-type music)
	(begin (display "music:")
			(disp (ly:music? music)) 
			(display "music-list:")
			(disp (ly:music-list? music))
			(display "context:")
			(disp (ly:context? music))
			(display "duration:")
			(disp (ly:duration? music))))
(define fun (cdr(assq 'test function-value-list)))
(display  (fun 2))
;(display (car (assoc note node-children-list)) )



;to do the same as get_pitch type get_properties music (lambda (x) (ly:music-property x 'pitch) ly:pitch?)
(define (get_properties music fun fun?)
    (let    ((elem (fun music))
            (elements (get_elements music))
            (element (get_element music)))
            (apply
            append  (if (fun? elem) (list elem) '())
                    (if (pair? elements) (apply append (map (lambda(x) (get_prop x fun fun?)) elements)) '())
                    (if (ly:music? element) (get_prop element fun fun?) '())
                    '())))
;; fun? : recognize the property searched (ex : ly:duration?)  
;; fun  : function to fetch the right object : (ex ly:music-property x                   
(define (get_elements_with_property music fun fun?)
    (let    ((elem (fun music))
            (elements (get_elements music))
            (element (get_element music)))
            (apply
            append  (if (fun? elem) (list music) '())
                    (if (pair? elements) (apply append (map (lambda(x) (get_elements_with_property x fun fun?)) elements)) '())
                    (if (ly:music? element) (get_elements_with_property element fun fun?) '())
                    '())))

(define (get_elements_by_name music name) 
    (get_elements_with_property music get_name (lambda(x) (eq? x name))))

(define (parse_seq_music list)
    (define (loop part_children_list l queue)
        (if (null? l)
            (reverse (cons (make-xml-node 'measure '() (reverse queue) ) part_children))
            ((let ((first (car l))
                    (remain (cdr l)))
                (case  (get_name first)
                    ('BarCheck (loop (cons (make-xml-node 'measure '() (reverse queue) ) part_children_list) remain '()))
                    ('NoteEvent (loop part_children_list remain (cons (parse_NoteEvent) queue)))))))))

(define (get_property music name fun)
    (let (elem (car (get_elements_by_name music name)))
        (fun elem)))

(define (retrieve_object music name)
    (let* (  (callbacks (cdr (assq name retrieve-object-function-list)) )
             (if (pair? callbacks)
                (let* ( (fun (car callbacks))
                        (fun? (cdr callbacks)))
                    (if (is_property name)
                        (get_properties music fun fun?)
                        (get_elements_with_property music fun fun?)))
                (#f)))))  

;*************************************************************************************************************************************





