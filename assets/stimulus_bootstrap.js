import { startStimulusApp } from '@symfony/stimulus-bundle';
import SimuladorController from './controllers/simulador_controller.js';

const app = startStimulusApp();
app.register('simulador', SimuladorController);
