import { MORPH_MAP, MORPH_MAP_MODELS, getMorphMapModel } from './morphMap.js';

function equal(actual, expected) {
  if (actual !== expected) {
    throw new Error(`Expected ${expected}, received ${actual}`);
  }
}

equal(MORPH_MAP.USER, 'user');
equal(MORPH_MAP.CLIENT, 7);
equal(MORPH_MAP_MODELS.user, 'User');
equal(MORPH_MAP_MODELS[7], 'Client');
equal(getMorphMapModel(MORPH_MAP.USER), 'User');
equal(getMorphMapModel(MORPH_MAP.CLIENT), 'Client');
equal(getMorphMapModel('missing'), 'Unknown');
equal(getMorphMapModel(99), 'Unknown');
