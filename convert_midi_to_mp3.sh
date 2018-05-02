#!/bin/sh
mv png/$1.midi midi/
timidity -Ow -o wav/$2.wav  midi/$1.midi 
lame -f wav/$2.wav mp3/$2.mp3 
rm wav/$2.wav
rm midi/$1.midi

