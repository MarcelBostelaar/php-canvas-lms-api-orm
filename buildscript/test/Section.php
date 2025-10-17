{
    "filename": "Section.php",
    "fields": [
        {
            "type": "string",
            "name": "name"
        },
        {
            "type": "CanvasApiLibrary\\Models\\Course",
            "name": "course"
        }
    ],
    "fieldsNullable": [],
    "plurals": [
        "Sections"
    ]
}array(
    0: Stmt_Namespace(
        name: Name(
            name: CanvasApiLibrary\Models
        )
        stmts: array(
            0: Stmt_Use(
                type: TYPE_NORMAL (1)
                uses: array(
                    0: UseItem(
                        type: TYPE_UNKNOWN (0)
                        name: Name(
                            name: CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel
                        )
                        alias: null
                    )
                )
            )
            1: Stmt_Use(
                type: TYPE_NORMAL (1)
                uses: array(
                    0: UseItem(
                        type: TYPE_UNKNOWN (0)
                        name: Name(
                            name: Src\Models\Generated\SectionProperties
                        )
                        alias: null
                    )
                )
            )
            2: Stmt_Class(
                attrGroups: array(
                )
                flags: FINAL (32)
                name: Identifier(
                    name: Section
                )
                extends: Name_FullyQualified(
                    name: CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel
                )
                implements: array(
                )
                stmts: array(
                    0: Stmt_TraitUse(
                        traits: array(
                            0: Name_FullyQualified(
                                name: Src\Models\Generated\SectionProperties
                            )
                        )
                        adaptations: array(
                        )
                    )
                    1: Stmt_Property(
                        attrGroups: array(
                        )
                        flags: PROTECTED | STATIC (10)
                        type: Identifier(
                            name: array
                        )
                        props: array(
                            0: PropertyItem(
                                name: VarLikeIdentifier(
                                    name: properties
                                )
                                default: Expr_Array(
                                    items: array(
                                        0: ArrayItem(
                                            key: null
                                            value: Expr_Array(
                                                items: array(
                                                    0: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: string
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                    1: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: name
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                )
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                        1: ArrayItem(
                                            key: null
                                            value: Expr_Array(
                                                items: array(
                                                    0: ArrayItem(
                                                        key: null
                                                        value: Expr_ClassConstFetch(
                                                            class: Name_FullyQualified(
                                                                name: CanvasApiLibrary\Models\Course
                                                            )
                                                            name: Identifier(
                                                                name: class
                                                            )
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                    1: ArrayItem(
                                                        key: null
                                                        value: Scalar_String(
                                                            value: course
                                                        )
                                                        byRef: false
                                                        unpack: false
                                                    )
                                                )
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                        )
                        hooks: array(
                        )
                    )
                    2: Stmt_Property(
                        attrGroups: array(
                        )
                        flags: PUBLIC | STATIC (9)
                        type: Identifier(
                            name: array
                        )
                        props: array(
                            0: PropertyItem(
                                name: VarLikeIdentifier(
                                    name: plurals
                                )
                                default: Expr_Array(
                                    items: array(
                                        0: ArrayItem(
                                            key: null
                                            value: Scalar_String(
                                                value: Sections
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                        )
                        hooks: array(
                        )
                    )
                )
            )
        )
    )
)