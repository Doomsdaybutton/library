SELECT (SELECT id FROM media WHERE id = 2242) as current_medium,
IF((SELECT medium_group FROM media WHERE id = 2242) != -1, (
	/* für gruppe */
	/* biggest volume in group -> group*/
	SELECT IF((SELECT id FROM media WHERE volume = (SELECT MAX(volume) FROM media WHERE volume < (SELECT volume FROM media WHERE id = 2242) AND medium_group = (SELECT medium_group FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = (SELECT medium_group FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242) LIMIT 1), (
		/* set */
		SELECT id FROM media WHERE volume = (SELECT MAX(volume) FROM media WHERE (volume < (SELECT volume FROM media WHERE id = 2242)) AND (medium_group = (SELECT medium_group FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = (SELECT medium_group FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242)
	), (
		/* not set */
		/* grösstes volume von grösster gruppe des gleichen autors -> autor*/
		SELECT IF((SELECT id FROM media WHERE medium_group = (SELECT MAX(medium_group) FROM media WHERE medium_group < (SELECT medium_group FROM media WHERE id = 2242) AND medium_group != -1 AND main_category = (SELECT main_category FROM media WHERE id  = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242) LIMIT 1), (
			/* set */
			SELECT id FROM media WHERE medium_group = (SELECT MAX(medium_group) FROM media WHERE medium_group < (SELECT medium_group FROM media WHERE id = 2242) AND medium_group != -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242) ORDER BY volume DESC LIMIT 1
		), (
			/* not set */
			/* check ob bücher ohne gruppe im vorherigen autor mit gleicher kategorie */
			SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
				/* es gibt autor ohne gruppe grade vorher */
				SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY title DESC LIMIT 1
			), (
				/* check for category change / no books without group)) */
				SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
					/* bücher von vorherigem autor sind in einer gruppe */
                    SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = (SELECT MAX(medium_group) FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242))) AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY volume DESC LIMIT 1
                ), (
					/* bücher sind in einer anderen kategorie */
                    SELECT IF((SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = -1 LIMIT 1), (
						/* cat before has books without group */
                        SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = -1 ORDER BY title DESC LIMIT 1
                        ), (
                        /* cat before has only group or doesnt exist */
                        SELECT IF((SELECT id FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
							/* cat exists thus group */
                            SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = (SELECT MAX(medium_group) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)))) ORDER BY volume DESC LIMIT 1
                            ), (
                            /* cat doesnt exist */
                            "first book in library"
						))
					))
                ))
			))
		))
	))
), (
	SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND title < (SELECT title FROM media WHERE id = 2242) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
		/* es gibt bücher vorher ohne gruppe */
		SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND title < (SELECT title FROM media WHERE id = 2242) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY title DESC LIMIT 1
		), (
		/* autor wechseln oder gruppe -> gibt es gruppen im autor*/
		SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group != -1 LIMIT 1), (
			/* es gibt gruppen im autor */
			SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = (SELECT MAX(medium_group) WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1 ORDER BY volume DESC LIMIT 1
			), (
			/* es gibt keine gruppen im autor -> autor wechseln */
			SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
				/* es gibt autor ohne gruppe grade vorher */
				SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY title DESC LIMIT 1
				), (
				/* check for category change / no books without group)) */
				SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
					/* bücher von vorherigem autor sind in einer gruppe */
					SELECT id FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = (SELECT MAX(medium_group) FROM media WHERE medium_index = (SELECT MAX(medium_index) FROM media WHERE medium_index < (SELECT medium_index FROM media WHERE id = 2242))) AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY volume DESC LIMIT 1
					), (
					/* bücher sind in einer anderen kategorie -> none */
					SELECT IF((SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = -1 LIMIT 1), (
						/* cat before has books without group */
						SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = -1 ORDER BY title DESC LIMIT 1
						), (
						/* cat before has only group or doesnt exist */
						SELECT IF((SELECT id FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
							/* cat exists thus group */
							SELECT id FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242))) AND medium_group = (SELECT MAX(medium_group) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MAX(medium_index) FROM media WHERE main_category = (SELECT MAX(main_category) FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242)))) ORDER BY volume DESC LIMIT 1
							), (
							/* cat doesnt exist */
							"first book in library"
						))
					))
				))
			))
		))
	))
)) as medium_before,
IF((SELECT medium_group FROM media WHERE id = 2242) != -1, (
/* group */
SELECT IF((SELECT id FROM media WHERE medium_group = (SELECT medium_group FROM media WHERE id = 2242) AND volume > (SELECT volume FROM media WHERE id = 2242) LIMIT 1), (
	SELECT id FROM media WHERE medium_group = (SELECT medium_group FROM media WHERE id = 2242) AND volume > (SELECT volume FROM media WHERE id = 2242) ORDER BY volume ASC LIMIT 1
    ), (
    SELECT IF((SELECT id FROM media WHERE medium_group = (SELECT MIN(medium_group) FROM media WHERE medium_group > (SELECT medium_group FROM media WHERE id = 2242) AND medium_group != -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242) LIMIT 1), (
		SELECT id FROM media WHERE medium_group = (SELECT MIN(medium_group) FROM media WHERE medium_group > (SELECT medium_group FROM media WHERE id = 2242) AND medium_group != -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_index = (SELECT medium_index FROM media WHERE id = 2242) ORDER BY volume ASC LIMIT 1
        ), (
        /* nicht in einer gruppe */
        SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND medium_group = -1 LIMIT 1), (
			SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND medium_group = -1 ORDER BY title ASC LIMIT 1
            ), (
            /*keine bücher ohne grupp im autor -> autor wechseln */
            SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group != -1 LIMIT 1), (
				SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = (SELECT MIN(medium_group) FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1) ORDER BY volume ASC LIMIT 1
                ), (
                /* nächster autor hat keine gruppe oder leztes buch in kategorie*/
                SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
					/* nächster autor hat keine gruppe */
                    SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = -1) AND medium_group = -1 AND main_category = (SELECT main_category FROM media WHERE id = 2242) ORDER BY title ASC LIMIT 1
                    ), (
                    /* kategorie wechseln: hat nächste kategorie gruppe? */
                    SELECT IF((SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242))) AND medium_group != -1 LIMIT 1), (
						/* next category has group */
                        SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242))) AND medium_group != -1 AND medium_group = (SELECT MIN(medium_group) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)))) ORDER BY volume ASC LIMIT 1
                        ), (
                        /* next category has no group or doesnt exist */
                        SELECT IF((SELECT id FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
							/* next category exists thus has no gruop*/
                            SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242))) ORDER BY title ASC LIMIT 1
                            ), (
                            /* next category doesnt exist */
                            "last book in library"
                            ))
                        ))
                    ))
                ))
            ))
        ))
    ))
), (
	SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = -1 AND title > (SELECT title FROM media WHERE id = 2242) ORDER BY title ASC LIMIT 1), (
		SELECT id FROM media WHERE medium_index = (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = -1 AND title > (SELECT title FROM media WHERE id = 2242) ORDER BY title ASC LIMIT 1
		), (
		/* switch author*/
		SELECT IF((SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1 LIMIT 1), (
			SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1 AND medium_group = (SELECT MIN(medium_group) FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1) ORDER BY volume ASC LIMIT 1
			), (
			/* no group or no author */
			SELECT IF((SELECT id FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
				SELECT id FROM media WHERE medium_index = (SELECT MIN(medium_index) FROM media WHERE medium_index > (SELECT medium_index FROM media WHERE id = 2242) AND main_category = (SELECT main_category FROM media WHERE id = 2242)) AND main_category = (SELECT main_category FROM media WHERE id = 2242) AND medium_group = -1 ORDER BY title ASC LIMIT 1
				), (
				/* has next cat group? */
				SELECT IF((SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_group != -1 AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)))LIMIT 1), (
					/* yes! */
					SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_group = (SELECT MIN(medium_group) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242))) AND medium_group != -1) ORDER BY volume ASC LIMIT 1
					), (
					/* no */
					/* does cat exist? */
					SELECT IF((SELECT id FROM media WHERE main_category < (SELECT main_category FROM media WHERE id = 2242) LIMIT 1), (
						/* yes ! */
						SELECT id FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242)) AND medium_index = (SELECT MIN(medium_index) FROM media WHERE main_category = (SELECT MIN(main_category) FROM media WHERE main_category > (SELECT main_category FROM media WHERE id = 2242))) ORDER BY title ASC LIMIT 1
						), (
						/* no */
						"last book in library"
					))
				))
			))
		))
	))
)) as medium_after
