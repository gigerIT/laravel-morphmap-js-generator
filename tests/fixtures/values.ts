import { MORPH_MAP, MORPH_MAP_MODELS, getMorphMapModel, type MorphMapValue } from './morphMap';

function equal(actual: unknown, expected: unknown): void {
  if (actual !== expected) {
    throw new Error(`Expected ${expected}, received ${actual}`);
  }
}

const stringValue: MorphMapValue = 'user';
const numericValue: MorphMapValue = 7;
equal(MORPH_MAP.USER, stringValue);
equal(MORPH_MAP.CLIENT, numericValue);
equal(MORPH_MAP_MODELS[stringValue], 'User');
equal(MORPH_MAP_MODELS[numericValue], 'Client');
equal(getMorphMapModel(MORPH_MAP.USER), 'User');
equal(getMorphMapModel(MORPH_MAP.CLIENT), 'Client');
equal(getMorphMapModel('user'), 'User');
equal(getMorphMapModel(7), 'Client');

// Invalid values are rejected statically; untyped runtime callers keep the fallback.
// @ts-expect-error Constant names are not morph values.
equal(getMorphMapModel('USER'), 'Unknown');
// @ts-expect-error Unknown strings are not morph values.
equal(getMorphMapModel('missing'), 'Unknown');
// @ts-expect-error Unknown numbers are not morph values.
equal(getMorphMapModel(99), 'Unknown');
