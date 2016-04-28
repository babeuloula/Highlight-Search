<?php

    class HighlightSearch {

        private $results = array();
        private $o = array(
            'each_side' => 5,
            'tag_name'  => 'span',
            'class'     => 'highlighted'
        );

        public function __construct ($options = array()) {
            $this->o = array_merge($this->o, $options);
        }

        public function search($text, $keywords) {
            $regEx = '/' . implode('|', $keywords) . '/i';

            $words = explode(' ', strip_tags($text));

            $regEx_results = array();
            foreach($words as $key => $word) {
                if(preg_match($regEx, $word, $matches)) {
                    $regEx_results[] = array(
                        'pos'     => $key,
                        'matches' => $matches,
                    );
                }
            }

            $last_pos = 0;
            foreach($regEx_results as $key => $regEx_result) {
                $p = $regEx_result['pos'];
                $matche = $regEx_result['matches'][0];

                $words[$p] = str_replace($matche, '<' . $this->o['tag_name'] . ' class="' . $this->o['class'] . ' ' . $this->o['class'] . '_' . (array_search($matche, $keywords) + 1) . '">' . $matche . '</' . $this->o['tag_name'] . '>', $words[$p]);

                $this->results[$key] = array('[...]');

                if(($p - $last_pos) > $this->o['each_side']) {
                    $i = $this->o['each_side'];
                    while($i != 0) {
                        $this->results[$key][] = $words[$p - $i];
                        $i = $i - 1;
                    }
                }

                $this->results[$key][] = $words[$p];

                if(isset($regEx_results[$key + 1])) {
                    $next = $regEx_results[$key + 1];
                    if($next['pos'] - $p > $this->o['each_side']) {
                        $i = 1;
                        while($i != ($this->o['each_side'] + 1)) {
                            if(isset($words[$p + $i])) {
                                $this->results[$key][] = $words[$p + $i];
                                $i = $i + 1;
                            } else {
                                break;
                            }
                        }
                    }
                } else {
                    $i = 1;
                    while($i != ($this->o['each_side'] + 1)) {
                        if(isset($words[$p + $i])) {
                            $this->results[$key][] = $words[$p + $i];
                            $i = $i + 1;
                        } else {
                            break;
                        }
                    }

                    if(isset($words[$p + $this->o['each_side']])) {
                        $this->results[$key][] = '[...]';
                    }
                }

                $last_pos = $p;
            }
        }

        public function getResults() {
            return array_map(function($n) {
                return implode(' ', $n);
            }, $this->results);
        }
    }
