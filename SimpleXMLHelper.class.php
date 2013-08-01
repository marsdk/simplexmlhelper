<?php

/*
 * A helper class for converting SimpleXMLElement to an array structure.
 *
 * @file Helper.class.php
 * @author Marcin Slezak <mars@mixpoint.dk>
 */

class SimpleXMLHelper {

  /**
   * Convert SimpleXML to object.
   *
   * @param SimpleXML $xml
   *   A SimpleXML resource.
   * @param array $ns
   *   An array with namespaces.
   * @param type $root
   *   Indicate whether this is a root
   *
   * @return mixed
   *   String with or array with the converted SimpleXML.
   */
  static public function xmlarray($xml, $ns = array(), $root = TRUE) {

    // Initialize this to false, as we will return the given value, if
    // we do not discover any children nodes while traversing.
    $found_children = FALSE;

    // This search the XML part that do not use namespaces.
    if ($xml->children()) {
      $found_children = TRUE;
      foreach ($xml->children() as $element => $node) {
        $element_count = count($xml->{$element});

        // If we do not have this element in our result, initialize it.
        if (!isset($result_array[$element])) {
          $result_array[$element] = "";
        }

        // Handle the attributes for an XML tag.
        $attributes = $node->attributes();
        if ($attributes) {
          $data = array(
              'attributes' => array(),
              'value' => (count($node) > 0) ? SimpleXMLHelper::xmlarray($node, $ns, FALSE) : (string) SimpleXMLHelper::xmlarray($node)
          );

          // Add the attributes as key-value pairs.
          foreach ($attributes as $attr => $value) {
            $data['attributes'][$attr] = (string) $value;
          }

          if ($element_count > 1) {
            // There are multiple elements here, so add as an array.
            $result_array[$element][] = $data;
          } else {
            // One element, just add it.
            $result_array[$element] = $data;
          }
        } else {
          // This is a value element, so we add it.
          if ($element_count > 1) {
            // There are multiple elements here, so add as an array.
            $result_array[$element][] = SimpleXMLHelper::xmlarray($node, $ns, FALSE);
          } else {
            // One element, just add it.
            $result_array[$element] = SimpleXMLHelper::xmlarray($node, $ns, FALSE);
          }
        }
      }
    }

    // This traverses through the namespaced XML part.
    foreach ($ns as $nskey => $nspath) {

      // If the namespace key is empty, we already did it above, so we skip
      // this part.
      if ($nskey == "") {
        continue;
      }

      // Get the children from the current namespace.
      if ($xml->children($nspath)) {

        // We have children. Do not return string value later.
        $found_children = TRUE;

        foreach ($xml->children($nspath) as $element => $node) {
          $element_count = count($xml->{$element});

          // Initialize element with namespace.
          if (!isset($result_array['ns_' . $nskey][$element])) {
            $result_array['ns_' . $nskey][$element] = "";
          }

          // Handle the attributes for an XML tag.
          $attributes = $node->attributes();
          if ($attributes) {
            $data = array(
                'attributes' => array(),
                'value' => (count($node) > 0) ? SimpleXMLHelper::xmlarray($node, $ns,  FALSE) : (string) $node
            );

            // Add the attributes as key-value pairs.
            foreach ($attributes as $attr => $value) {
              $data['attributes'][$attr] = (string) $value;
            }

            if ($element_count > 1) {
              $result_array['ns_' . $nskey][$element][] = $data;
            } else {
              $result_array['ns_' . $nskey][$element] = $data;
            }
          } else {
            // This is a value.
            if ($element_count > 1) {
              $result_array['ns_' . $nskey][$element][] = SimpleXMLHelper::xmlarray($node, $ns,  FALSE);
            } else {
              $result_array['ns_' . $nskey][$element] = SimpleXMLHelper::xmlarray($node, $ns,  FALSE);
            }
          }
        }
      }
    }

    // No children was found. Return valie.
    if (!$found_children) {
      return (string) $xml;
    }

    if ($root) {
      return array($xml->getName() => $result_array);
    } else {
      return $result_array;
    }
  }

  /**
   * Get the value from a SimpleXMLElement.
   *
   * @param SimpleXMLElement $element
   *   A SimpleXMLElement.
   *
   * @return string
   *   String value from SimpleXMLElement.
   */
  static public function getValueFromElement($element) {
    $json = json_encode($element);
    $array = json_decode($json, TRUE);
    return (string) $array[0];
  }

  /**
   * Debug a SimpleXMLElement element.
   *
   * @param SimpleXMLElement $element
   *   A SimpleXMLElement element.
   * @param boolean $return
   *   If set to TRUE it will return the array instead of outputting.
   *
   * @return array
   *   Return an array with the SimpleXMLElement data on it.
   */
  static public function simpleXMLDebug($element, $return = FALSE) {
    $json = json_encode($element);
    $array = json_decode($json, TRUE);

    if (!$return) {
      var_dump($array);
    }
    else {
      return $array;
    }
  }

}
