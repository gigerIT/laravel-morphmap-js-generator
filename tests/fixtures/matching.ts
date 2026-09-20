import { MORPH_MAP, getMorphMapModel } from './morphMap';

const model: string = getMorphMapModel(MORPH_MAP.X);
if (model !== 'MatchingModel') {
  throw new Error(`Expected MatchingModel, received ${model}`);
}
