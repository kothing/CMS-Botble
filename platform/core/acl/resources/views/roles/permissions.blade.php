<ul id='auto-checkboxes' data-name='foo' class="list-unstyled list-feature">
    <li id="mainNode">
        <input type="checkbox" id="expandCollapseAllTree">&nbsp;&nbsp;
        <label for="expandCollapseAllTree" class="label label-default allTree">{{ trans('core/acl::permissions.all') }}</label>
        <ul>
            @foreach ($children['root'] as $elementKey => $element)
                <li class="collapsed" id="node{{ $elementKey }}">
                    <input type="checkbox" id="checkSelect{{ $elementKey }}" name="flags[]" value="{{ $flags[$element]['flag'] }}" @if (in_array($flags[$element]['flag'], $active)) checked @endif>
                    <label for="checkSelect{{ $elementKey }}" class="label label-warning" style="margin: 5px;">{{ $flags[$element]['name'] }}</label>
                    @if (isset($children[$element]))
                        <ul>
                            @foreach($children[$element] as $subKey => $subElements)
                                <li class="collapsed" id="node_sub_{{ $elementKey  }}_{{ $subKey }}">
                                    <input type="checkbox" id="checkSelect_sub_{{ $elementKey  }}_{{ $subKey }}" name="flags[]" value="{{ $flags[$subElements]['flag'] }}" @if (in_array($flags[$subElements]['flag'], $active)) checked @endif>
                                    <label for="checkSelect_sub_{{ $elementKey  }}_{{ $subKey }}" class="label label-primary nameMargin">{{ $flags[$subElements]['name'] }}</label>
                                    @if (isset($children[$subElements]))
                                        <ul>
                                            @foreach ($children[$subElements] as $subSubKey => $subSubElements)
                                                <li class="collapsed" id="node_sub_sub_{{ $subSubKey }}">
                                                    <input type="checkbox" id="checkSelect_sub_sub{{ $subSubKey }}" name="flags[]" value="{{ $flags[$subSubElements]['flag'] }}" @if (in_array($flags[$subSubElements]['flag'], $active)) checked @endif>
                                                    <label for="checkSelect_sub_sub{{ $subSubKey }}" class="label label-success nameMargin">{{ $flags[$subSubElements]['name'] }}</label>
                                                    @if (isset($children[$subSubElements]))
                                                        <ul>
                                                            @foreach($children[$subSubElements] as $grandChildrenKey => $grandChildrenElements)
                                                                <li class="collapsed" id="node_grand_child{{ $grandChildrenKey }}">
                                                                    <input type="checkbox" id="checkSelect_grand_child{{ $grandChildrenKey }}" name="flags[]" value="{{ $flags[$grandChildrenElements]['flag'] }}" @if (in_array($flags[$grandChildrenElements]['flag'], $active)) checked @endif>
                                                                    <label for="checkSelect_grand_child{{ $grandChildrenKey }}" class="label label-danger nameMargin">{{ $flags[$grandChildrenElements]['name'] }}</label>
                                                                    @if (isset($children[$grandChildrenElements]))
                                                                        <ul>
                                                                            @foreach ($children[$grandChildrenElements] as $grandChildrenKeySub => $greatGrandChildrenElements)
                                                                                <li class="collapsed" id="node{{ $grandChildrenKey }}">
                                                                                    <input type="checkbox" id="checkSelect_grand_child{{ $grandChildrenKeySub }}" name="flags[]" value="{{ $flags[$grandChildrenElements]['flag'] }}" @if (in_array($flags[$grandChildrenElements]['flag'], $active)) checked @endif>
                                                                                    <label for="checkSelect_grand_child{{ $grandChildrenKeySub }}" class="label label-info nameMargin">{{ $flags[$grandChildrenElements]['name'] }}</label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
</ul>
