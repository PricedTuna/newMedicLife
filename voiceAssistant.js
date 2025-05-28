// voiceAssistant.js
const VoiceAssistant = (() => {
  let active = false;
  let currentAudio = null;

  const audioMap = {
    '/medics': '/audio/medics.mp3',
    '/patients': '/audio/patients.mp3',
    '/appointments': '/audio/appointments.mp3',
  };

  function getAudioForPath(path) {
    for (const key in audioMap) {
      if (path.includes(key)) {
        return audioMap[key];
      }
    }
    return audioMap['/']; // default
  }

  function play() {
    if (active) return; // ya está activo
    active = true;
    const path = window.location.pathname;
    const audioFile = getAudioForPath(path);

    if(currentAudio){
      currentAudio.pause();
      currentAudio.currentTime = 0;
    }

    currentAudio = new Audio(audioFile);
    currentAudio.play();
  }

  function stop() {
    if(!active) return;
    active = false;
    if(currentAudio){
      currentAudio.pause();
      currentAudio.currentTime = 0;
    }
  }

  function toggle() {
    if (active) stop();
    else play();
  }

  return {
    play,
    stop,
    toggle,
    isActive: () => active
  };
})();
