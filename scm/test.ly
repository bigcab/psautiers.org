\version "2.12.3"
#(module-define! (resolve-module '(guile-user))
                 'lilypond-module (current-module))

#(display %load-path)
#(use-modules (ice-9 regex)
	     (srfi srfi-1)
	     (oop goops)
	     (lily)
	     (scm xml_class)
	     )




traLaLa = { c'4 d'4 }

PartPOneVoiceOne =  {b }

PartPOneVoiceThree =   {a }

PartPOneVoiceTwo =  {d}

PartPOneVoiceFour =   {
    c }

testl=\new PianoStaff <<
    \set PianoStaff.instrumentName = "Piano"
    \context Staff = "1" << 
        \context Voice = "PartPOneVoiceOne" { \voiceOne \PartPOneVoiceOne }
        \context Voice = "PartPOneVoiceTwo" { \voiceTwo \PartPOneVoiceTwo }
        >> \context Staff = "2" <<
        \context Voice = "PartPOneVoiceThree" { \voiceOne \PartPOneVoiceThree }
        \context Voice = "PartPOneVoiceFour" { \voiceTwo \PartPOneVoiceFour }
        >>
    >>



#(define test #{c'4 | d'4#})
#(define port (current-output-port))

%% begin of cab_to_xml code



