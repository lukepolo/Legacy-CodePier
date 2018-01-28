import NProgress from "nprogress";
window.NProgress = NProgress;

export default function loadProgressBar(config) {
  let timeoutSet = false;
  let requestsCounter = 0;
  let spinnerTimeout = null;

  const setupStartProgress = () => {
    axios.interceptors.request.use(config => {
      if (timeoutSet === false) {
        spinnerTimeout = setTimeout(() => {
          turnOnSpinner();
        }, 1000);
        timeoutSet = true;
      }

      requestsCounter++;
      NProgress.start();

      return config;
    });
  };

  const setupStopProgress = () => {
    const responseFunc = response => {

      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return response;
    };

    const errorFunc = error => {
      if (--requestsCounter === 0) {
        turnOffSpinner();
        NProgress.done();
      }
      return Promise.reject(error);
    };

    axios.interceptors.response.use(responseFunc, errorFunc);
  };

  const turnOnSpinner = () => {
    let spinnerElement = getSpinnerElement();

    if (!spinnerElement) {
      let spinner = document.createElement('div');
      spinner.id = 'app-spinner';
      spinner.classList.add('sk-container');

      let cube = document.createElement('div');
      cube.classList.add('sk-folding-cube');
      for(let i = 1; i <= 4; i++) {
        let cubePart = document.createElement('div')
        cubePart.classList.add(`sk-cube`);
        cubePart.classList.add(`sk-cube${i}`);
        cube.appendChild(cubePart);
      }
      spinner.appendChild(cube);

      document.body.appendChild(spinner);
    }
  }

  const getSpinnerElement = () => {
    return document.getElementById("app-spinner");
  }

  const turnOffSpinner = () => {
    timeoutSet = false;
    clearTimeout(spinnerTimeout);
    spinnerTimeout = null;
    let spinnerElement = getSpinnerElement();
    if (spinnerElement) {
      spinnerElement.remove();
    }
  }

  NProgress.configure(config);
  setupStartProgress();
  setupStopProgress();
}
