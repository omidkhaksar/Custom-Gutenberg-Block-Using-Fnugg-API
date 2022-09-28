import * as React from 'react';
import TextField from '@mui/material/TextField';
import Stack from '@mui/material/Stack';
import Autocomplete from '@mui/material/Autocomplete';


/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
  const { attributes, setAttributes } = props
	const { resortName } = attributes

  const [value, setValue] = React.useState(resortName);
  const [inputValue, setInputValue] = React.useState(resortName);
  const [jsonResult, setJsonResult] = React.useState([]);

  React.useEffect(() => {
    fetch(`https://api.fnugg.no/suggest/autocomplete?q=${inputValue}`)
      .then((response) => response.json())
      .then((json) => setJsonResult(json.result))
  }, [inputValue])
  if(jsonResult !==[]){
    return (
      <div {...useBlockProps()}>
      <h3>Write Resort Name To Search:</h3>
      <Stack>
        <Autocomplete
          id="controllable-states-demo"
          // getOptionLabel={(jsonResult) => `${jsonResult.name}`}
          options={jsonResult.map(({name})=>{return(name)})}
          value={value}
          onChange={(event, newValue) => {
            setValue(newValue);
            setAttributes({ resortName: String(newValue) });
          }}
          inputValue={inputValue}
          onInputChange={(event, newInputValue) => {
            setInputValue(newInputValue);
          }}

          sx={{ width: 300 }}
          renderInput={(params) => <TextField {...params} label="Search For Resort" />}
        />
      </Stack>
      </div>
    );
  }else{
    return <p>loading...</p>;
  }
}
